<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role'); // parent, driver
            $table->string('request_type'); // pickup_change, absence, maintenance, leave, schedule, etc.
            $table->string('subject');
            $table->text('description');
            $table->string('priority')->default('medium'); // low, medium, high
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // children, dates, locations, etc.
            $table->string('status')->default('pending'); // pending, in-progress, resolved, rejected
            $table->text('admin_response')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'role']);
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
