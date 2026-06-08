<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ReportExportService
{
    public function toCsv(Collection|array $rows, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = collect($rows);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            if ($rows->isNotEmpty()) {
                $first = (array) $rows->first();
                fputcsv($out, array_keys($first));
                foreach ($rows as $row) {
                    fputcsv($out, array_values((array) $row));
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function toExcel(Collection|array $rows, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = collect($rows);
        $excelName = str_ends_with($filename, '.csv') ? str_replace('.csv', '.xls', $filename) : $filename . '.xls';

        return response()->streamDownload(function () use ($rows) {
            echo "\xEF\xBB\xBF";
            if ($rows->isNotEmpty()) {
                $first = (array) $rows->first();
                echo implode("\t", array_keys($first)) . "\n";
                foreach ($rows as $row) {
                    echo implode("\t", array_map(
                        fn ($v) => str_replace(["\t", "\n", "\r"], ' ', (string) $v),
                        array_values((array) $row)
                    )) . "\n";
                }
            }
        }, $excelName, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    public function toPdf(string $title, Collection|array $rows, string $filename): \Illuminate\Http\Response
    {
        $rows = collect($rows);
        $headers = $rows->isNotEmpty() ? array_keys((array) $rows->first()) : [];

        $html = view('exports.report-pdf', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ])->render();

        $pdf = $this->buildSimplePdf($title, $html);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function buildSimplePdf(string $title, string $htmlText): string
    {
        $text = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</tr>', '</p>'], "\n", $htmlText));
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

        $content = "BT /F1 10 Tf 50 780 Td\n";
        $y = 0;
        foreach ($lines as $line) {
            if ($y > 740) {
                break;
            }
            $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], mb_substr($line, 0, 90));
            $content .= "({$escaped}) Tj\n0 -14 Td\n";
            $y += 14;
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
        $objects[] = '4 0 obj << /Length ' . strlen($content) . " >> stream\n{$content}\nendstream endobj\n";
        $objects[] = "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . count($offsets) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer << /Size " . count($offsets) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }
}
