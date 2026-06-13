<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->unsignedInteger('student_count')->nullable()->after('principal_name');
            $table->unsignedInteger('bus_count')->nullable()->after('student_count');
            $table->string('operating_hours_start', 10)->nullable()->after('bus_count');
            $table->string('operating_hours_end', 10)->nullable()->after('operating_hours_start');
            $table->string('commercial_register')->nullable()->after('operating_hours_end');
            $table->string('insurance_document_path')->nullable()->after('license_document_path');
            $table->timestamp('profile_submitted_at')->nullable()->after('fleet_type');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'student_count',
                'bus_count',
                'operating_hours_start',
                'operating_hours_end',
                'commercial_register',
                'insurance_document_path',
                'profile_submitted_at',
            ]);
        });
    }
};
