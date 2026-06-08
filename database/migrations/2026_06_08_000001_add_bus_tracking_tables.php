<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->decimal('current_lat', 10, 7)->nullable()->after('active');
            $table->decimal('current_lng', 10, 7)->nullable()->after('current_lat');
            $table->decimal('current_speed', 8, 2)->nullable()->after('current_lng');
            $table->decimal('current_heading', 6, 2)->nullable()->after('current_speed');
            $table->timestamp('location_updated_at')->nullable()->after('current_heading');
        });

        Schema::create('bus_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('heading', 6, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['bus_id', 'recorded_at']);
            $table->index(['trip_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_locations');

        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn([
                'current_lat',
                'current_lng',
                'current_speed',
                'current_heading',
                'location_updated_at',
            ]);
        });
    }
};
