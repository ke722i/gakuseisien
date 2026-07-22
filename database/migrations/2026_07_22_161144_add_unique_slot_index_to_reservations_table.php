<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 同じ教室・同じ日・同じ時限に予約が二重登録されるのを、データベース側でも防ぐ。
 *
 * アプリ側では「重複がないか調べてから登録する」処理を入れているが、
 * ほぼ同時に2人が申請すると、どちらも「重複なし」と判定して両方登録され得る。
 * 最後の砦としてデータベースに一意制約を置く。
 *
 * 却下済み（rejected）の予約は枠を占有しないため、対象から除く部分インデックスにする。
 */
return new class extends Migration
{
    private const INDEX_NAME = 'reservations_active_slot_unique';

    public function up(): void
    {
        if (! Schema::hasTable('reservations')) {
            return;
        }

        // 部分インデックスはスキーマビルダーで表現できないため生SQLを使う
        DB::statement(
            'CREATE UNIQUE INDEX IF NOT EXISTS ' . self::INDEX_NAME
            . ' ON reservations (room_id, reservation_date, period)'
            . " WHERE status <> 'rejected'"
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS ' . self::INDEX_NAME);
    }
};
