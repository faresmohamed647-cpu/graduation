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
            $table->string('status')->default('pending')->after('active');
        });

        DB::table('parents')->orderBy('id')->each(function ($parent) {
            $hasStudents = DB::table('students')->where('parent_id', $parent->id)->exists();
            $status = 'pending';

            if ($parent->active && $hasStudents) {
                $status = 'approved';
            } elseif ($parent->active) {
                $status = 'pending_details';
            }

            DB::table('parents')->where('id', $parent->id)->update(['status' => $status]);
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
