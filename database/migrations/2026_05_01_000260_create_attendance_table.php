<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('dropped_off_at')->nullable();
            $table->string('status')->default('absent')->index(); // absent|picked_up|dropped_off|no_show
            $table->timestamps();

            $table->unique(['trip_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};

