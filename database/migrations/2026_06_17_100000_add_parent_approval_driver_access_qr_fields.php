<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->timestamp('profile_approved_at')->nullable()->after('status');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->json('dashboard_sections')->nullable()->after('status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->json('qr_payload')->nullable()->after('qr_code');
            $table->timestamp('qr_generated_at')->nullable()->after('qr_payload');
        });

        DB::table('parents')
            ->where('status', 'approved')
            ->whereNull('profile_approved_at')
            ->update(['profile_approved_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['qr_payload', 'qr_generated_at']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('dashboard_sections');
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('profile_approved_at');
        });
    }
};
