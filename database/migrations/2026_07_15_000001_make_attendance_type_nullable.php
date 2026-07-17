<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('attendance_reports') && Schema::hasColumn('attendance_reports', 'attendance_type')) {
            DB::statement('ALTER TABLE attendance_reports ALTER COLUMN attendance_type DROP NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendance_reports') && Schema::hasColumn('attendance_reports', 'attendance_type')) {
            DB::statement('ALTER TABLE attendance_reports ALTER COLUMN attendance_type SET NOT NULL');
        }
    }
};
