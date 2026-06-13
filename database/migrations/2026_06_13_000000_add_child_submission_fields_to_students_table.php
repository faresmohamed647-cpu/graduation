<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('full_name');
            }
            if (! Schema::hasColumn('students', 'pickup_location')) {
                $table->string('pickup_location')->nullable()->after('school_name');
            }
            if (! Schema::hasColumn('students', 'dropoff_location')) {
                $table->string('dropoff_location')->nullable()->after('pickup_location');
            }
            if (! Schema::hasColumn('students', 'pickup_time')) {
                $table->time('pickup_time')->nullable()->after('dropoff_location');
            }
            if (! Schema::hasColumn('students', 'dropoff_time')) {
                $table->time('dropoff_time')->nullable()->after('pickup_time');
            }
            if (! Schema::hasColumn('students', 'has_medical_condition')) {
                $table->boolean('has_medical_condition')->default(false)->after('dropoff_time');
            }
            if (! Schema::hasColumn('students', 'medical_condition')) {
                $table->text('medical_condition')->nullable()->after('has_medical_condition');
            }
            if (! Schema::hasColumn('students', 'medication')) {
                $table->text('medication')->nullable()->after('medical_condition');
            }
            if (! Schema::hasColumn('students', 'assignment_status')) {
                $table->string('assignment_status')->default('pending')->index()->after('active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            foreach ([
                'assignment_status',
                'medication',
                'medical_condition',
                'has_medical_condition',
                'dropoff_time',
                'pickup_time',
                'dropoff_location',
                'pickup_location',
                'age',
            ] as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
