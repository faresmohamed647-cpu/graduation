<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('school_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('school_id');
            $table->index(['school_id', 'active']);
            $table->index('bus_id');
            $table->index('bus_route_id');
            $table->index('qr_code');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->index('school_id');
        });

        Schema::table('buses', function (Blueprint $table) {
            $table->index('school_id');
        });

        Schema::table('bus_routes', function (Blueprint $table) {
            $table->index('school_id');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->index('school_id');
            $table->index(['school_id', 'trip_date']);
            $table->index(['trip_date', 'status']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['school_id', 'created_at']);
        });

        Schema::table('emergency_alerts', function (Blueprint $table) {
            $table->index(['school_id', 'status']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index(['role', 'status']);
            $table->index('email');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropIndex(['school_id']));
        Schema::table('students', fn (Blueprint $t) => $t->dropIndex(['school_id']));
        Schema::table('students', fn (Blueprint $t) => $t->dropIndex(['school_id', 'active']));
        Schema::table('students', fn (Blueprint $t) => $t->dropIndex(['bus_id']));
        Schema::table('students', fn (Blueprint $t) => $t->dropIndex(['bus_route_id']));
        Schema::table('students', fn (Blueprint $t) => $t->dropIndex(['qr_code']));
        Schema::table('drivers', fn (Blueprint $t) => $t->dropIndex(['school_id']));
        Schema::table('buses', fn (Blueprint $t) => $t->dropIndex(['school_id']));
        Schema::table('bus_routes', fn (Blueprint $t) => $t->dropIndex(['school_id']));
        Schema::table('trips', fn (Blueprint $t) => $t->dropIndex(['school_id']));
        Schema::table('trips', fn (Blueprint $t) => $t->dropIndex(['school_id', 'trip_date']));
        Schema::table('trips', fn (Blueprint $t) => $t->dropIndex(['trip_date', 'status']));
        Schema::table('activity_logs', fn (Blueprint $t) => $t->dropIndex(['school_id', 'created_at']));
        Schema::table('emergency_alerts', fn (Blueprint $t) => $t->dropIndex(['school_id', 'status']));
        Schema::table('applications', fn (Blueprint $t) => $t->dropIndex(['role', 'status']));
        Schema::table('applications', fn (Blueprint $t) => $t->dropIndex(['email']));
        Schema::table('reports', fn (Blueprint $t) => $t->dropIndex(['status', 'created_at']));
    }
};
