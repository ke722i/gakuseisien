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
        // PostgreSQL専用の生SQLのため、他ドライバ（テスト用SQLite等）ではスキップする。
        // ※新規作成されるDBでは create_notifications_table 側で最初から nullable のため実行不要。
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        if (Schema::hasTable('attendance_reports') && Schema::hasColumn('attendance_reports', 'attendance_type')) {
            DB::statement('ALTER TABLE attendance_reports ALTER COLUMN attendance_type DROP NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        if (Schema::hasTable('attendance_reports') && Schema::hasColumn('attendance_reports', 'attendance_type')) {
            DB::statement('ALTER TABLE attendance_reports ALTER COLUMN attendance_type SET NOT NULL');
        }
    }
};
