<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesSchoolScope;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\EmergencyAlert;
use App\Models\Student;
use App\Models\Trip;
use App\Services\ReportExportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class SchoolAdminReportController extends Controller
{
    use ResolvesSchoolScope;

    public function __construct(private readonly ReportExportService $exporter) {}

    public function index(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $type = $request->get('type', 'summary');

        return response()->json([
            'success' => true,
            'data' => match ($type) {
                'students' => $this->studentReport($schoolId),
                'drivers' => $this->driverReport($schoolId),
                'buses' => $this->busReport($schoolId),
                'attendance' => $this->attendanceReport($schoolId, $request),
                'safety' => $this->safetyReport($schoolId),
                'monthly' => $this->monthlyReport($schoolId),
                default => $this->summaryReport($schoolId),
            },
        ]);
    }

    public function export(Request $request)
    {
        $schoolId = $this->schoolId($request);
        $type = $request->get('type', 'students');
        $format = $request->get('format', 'csv');
        $rows = collect(match ($type) {
            'drivers' => $this->driverReport($schoolId),
            'buses' => $this->busReport($schoolId),
            'attendance' => $this->attendanceReport($schoolId, $request),
            'monthly' => $this->monthlyReport($schoolId),
            default => $this->studentReport($schoolId),
        });

        $base = "school-{$schoolId}-{$type}-" . now()->format('Y-m-d');

        return match ($format) {
            'pdf' => $this->exporter->toPdf('SafeStep School Report: ' . ucfirst($type), $rows, $base . '.pdf'),
            'xlsx', 'excel' => $this->exporter->toExcel($rows, $base . '.csv'),
            default => $this->exporter->toCsv($rows, $base . '.csv'),
        };
    }

    private function summaryReport(int $schoolId): array
    {
        return [
            'students' => Student::where('school_id', $schoolId)->count(),
            'drivers' => Driver::where('school_id', $schoolId)->count(),
            'buses' => Bus::where('school_id', $schoolId)->count(),
            'trips_this_month' => Trip::where('school_id', $schoolId)->whereMonth('trip_date', now()->month)->count(),
            'open_emergencies' => EmergencyAlert::where('school_id', $schoolId)->where('status', 'open')->count(),
        ];
    }

    private function monthlyReport(int $schoolId): array
    {
        $from = now()->subMonths(12);

        return Attendance::query()
            ->with('trip:id,trip_date')
            ->whereHas('trip', fn ($q) => $q->where('school_id', $schoolId)->where('trip_date', '>=', $from))
            ->get()
            ->groupBy(fn (Attendance $row) => CarbonImmutable::parse($row->trip?->trip_date)->format('Y-m'))
            ->flatMap(function ($items, $month) {
                return $items->groupBy('status')->map(fn ($group, $status) => [
                    'month' => $month,
                    'status' => $status,
                    'count' => $group->count(),
                ]);
            })
            ->sortBy('month')
            ->values()
            ->all();
    }

    private function studentReport(int $schoolId): array
    {
        return Student::where('school_id', $schoolId)
            ->with('parent.user')
            ->get()
            ->map(fn (Student $s) => [
                'id' => $s->id,
                'name' => $s->full_name,
                'grade' => $s->grade,
                'parent' => $s->parent?->user?->name,
                'status' => $s->active ? 'active' : 'inactive',
            ])
            ->all();
    }

    private function driverReport(int $schoolId): array
    {
        return Driver::where('school_id', $schoolId)
            ->with('user')
            ->get()
            ->map(fn (Driver $d) => [
                'id' => $d->id,
                'name' => $d->user?->name,
                'license' => $d->license_number,
                'trips' => $d->trips()->count(),
                'status' => $d->status,
            ])
            ->all();
    }

    private function busReport(int $schoolId): array
    {
        return Bus::where('school_id', $schoolId)
            ->get()
            ->map(fn (Bus $b) => [
                'id' => $b->id,
                'bus_number' => $b->bus_number,
                'plate' => $b->plate_number,
                'capacity' => $b->capacity,
                'status' => $b->status,
            ])
            ->all();
    }

    private function attendanceReport(int $schoolId, Request $request): array
    {
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to = $request->get('to', now()->toDateString());

        return Attendance::query()
            ->selectRaw('trips.trip_date as date, attendance.status, COUNT(*) as count')
            ->join('trips', 'attendance.trip_id', '=', 'trips.id')
            ->where('trips.school_id', $schoolId)
            ->whereBetween('trips.trip_date', [$from, $to])
            ->groupBy('trips.trip_date', 'attendance.status')
            ->orderBy('trips.trip_date')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function safetyReport(int $schoolId): array
    {
        return EmergencyAlert::where('school_id', $schoolId)
            ->selectRaw('type, severity, status, COUNT(*) as count')
            ->groupBy('type', 'severity', 'status')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }
}
