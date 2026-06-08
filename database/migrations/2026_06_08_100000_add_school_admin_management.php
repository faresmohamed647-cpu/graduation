<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('email');
            $table->string('principal_name')->nullable()->after('logo');
            $table->string('status')->default('active')->after('principal_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('role')->constrained('schools')->nullOnDelete();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('parent_id')->constrained('schools')->nullOnDelete();
            $table->string('photo')->nullable()->after('grade');
            $table->string('qr_code')->nullable()->after('photo');
            $table->string('rfid_tag')->nullable()->after('qr_code');
            $table->foreignId('bus_id')->nullable()->after('rfid_tag')->constrained('buses')->nullOnDelete();
            $table->foreignId('bus_route_id')->nullable()->after('bus_id')->constrained('bus_routes')->nullOnDelete();
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('user_id')->constrained('schools')->nullOnDelete();
        });

        Schema::table('buses', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->after('school_id')->constrained('drivers')->nullOnDelete();
            $table->foreignId('bus_route_id')->nullable()->after('driver_id')->constrained('bus_routes')->nullOnDelete();
            $table->date('insurance_expiry')->nullable()->after('capacity');
            $table->json('documents')->nullable()->after('insurance_expiry');
            $table->string('status')->default('active')->after('documents');
        });

        Schema::table('bus_routes', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
            $table->foreignId('bus_id')->nullable()->after('school_id')->constrained('buses')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->after('bus_id')->constrained('drivers')->nullOnDelete();
            $table->decimal('distance_km', 8, 2)->nullable()->after('estimated_minutes');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->string('severity')->default('high');
            $table->string('status')->default('open');
            $table->text('message')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_alerts');
        Schema::dropIfExists('activity_logs');

        Schema::table('trips', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('bus_routes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_id');
            $table->dropConstrainedForeignId('bus_id');
            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn('distance_km');
        });

        Schema::table('buses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bus_route_id');
            $table->dropConstrainedForeignId('driver_id');
            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn(['insurance_expiry', 'documents', 'status']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bus_route_id');
            $table->dropConstrainedForeignId('bus_id');
            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn(['photo', 'qr_code', 'rfid_tag']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['logo', 'principal_name', 'status']);
        });
    }
};
