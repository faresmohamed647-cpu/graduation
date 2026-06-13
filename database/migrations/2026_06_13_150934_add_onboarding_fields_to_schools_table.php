<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('status')->default('pending_details')->change();
            $table->string('license_number')->nullable()->after('status');
            $table->date('license_expiry')->nullable()->after('license_number');
            $table->string('license_document_path')->nullable()->after('license_expiry');
            $table->string('fleet_type')->nullable()->after('license_document_path');
            $table->boolean('active')->default(false)->after('fleet_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
            $table->dropColumn(['license_number', 'license_expiry', 'license_document_path', 'fleet_type', 'active']);
        });
    }
};
