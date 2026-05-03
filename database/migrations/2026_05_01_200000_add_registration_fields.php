<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Extra driver fields ──
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('state')->nullable()->after('phone');
            $table->string('full_name')->nullable()->after('state');
            $table->unsignedInteger('age')->nullable()->after('full_name');
            $table->string('gender')->nullable()->after('age');
            $table->string('car_type')->nullable()->after('gender');
            $table->string('car_model')->nullable()->after('car_type');
            $table->string('car_plate')->nullable()->after('car_model');
            $table->string('address')->nullable()->after('car_plate');
            $table->text('message')->nullable()->after('address');
            $table->string('status')->default('pending')->after('active'); // pending, interview_scheduled, approved, rejected
            $table->dateTime('interview_date')->nullable()->after('status');
        });

        // ── Extra parent fields ──
        Schema::table('parents', function (Blueprint $table) {
            $table->string('state')->nullable()->after('address');
            $table->string('relationship')->nullable()->after('state');      // Father / Mother
            $table->unsignedInteger('student_count')->nullable()->after('relationship');
            $table->string('degree')->nullable()->after('student_count');
            $table->string('education_system')->nullable()->after('degree');
            $table->string('school_name')->nullable()->after('education_system');
            $table->string('school_address')->nullable()->after('school_name');
            $table->string('school_starting')->nullable()->after('school_address');
            $table->text('message')->nullable()->after('school_starting');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'state', 'full_name', 'age', 'gender',
                'car_type', 'car_model', 'car_plate', 'address',
                'message', 'status', 'interview_date',
            ]);
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn([
                'state', 'relationship', 'student_count', 'degree',
                'education_system', 'school_name', 'school_address',
                'school_starting', 'message',
            ]);
        });
    }
};
