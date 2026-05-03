<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('type')->default('morning')->index(); // morning|afternoon|custom
            $table->json('stops')->nullable(); // [{name,lat,lng,order}, ...]
            $table->unsignedInteger('estimated_minutes')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_routes');
    }
};

