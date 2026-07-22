<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 通報テーブルに2つの不足を補う。
 *
 * 1. Q&Aの「回答」への通報先が無く、回答を通報すると同じIDの質問への通報として
 *    記録されてしまっていたため、answer_id を追加する。
 * 2. 同じ人が同じ投稿を何度でも通報できてしまうため、対象ごとに1人1回の制限をかける。
 */
return new class extends Migration
{
    /** 対象カラムごとの一意インデックス名 */
    private const UNIQUE_INDEXES = [
        'question_id' => 'reports_user_question_unique',
        'post_id' => 'reports_user_post_unique',
        'answer_id' => 'reports_user_answer_unique',
    ];

    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (! Schema::hasColumn('reports', 'answer_id')) {
                // Q&Aの回答への通報（それ以外の通報では null）
                $table->foreignId('answer_id')->nullable()->after('post_id')->constrained()->cascadeOnDelete();
            }
        });

        // 一意制約を張る前に、既にある重複通報を最も古い1件だけ残して片付ける
        foreach (array_keys(self::UNIQUE_INDEXES) as $column) {
            $this->removeDuplicateReports($column);
        }

        // 「対象が入っている行だけ」を対象にした一意インデックス。
        // 部分インデックスはスキーマビルダーで表現できないため生SQLを使う。
        foreach (self::UNIQUE_INDEXES as $column => $indexName) {
            DB::statement(
                "CREATE UNIQUE INDEX IF NOT EXISTS {$indexName}"
                . " ON reports (user_id, {$column})"
                . " WHERE user_id IS NOT NULL AND {$column} IS NOT NULL"
            );
        }
    }

    public function down(): void
    {
        foreach (self::UNIQUE_INDEXES as $indexName) {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");
        }

        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'answer_id')) {
                $table->dropConstrainedForeignId('answer_id');
            }
        });
    }

    /** 同じ利用者・同じ対象の通報を、最も古い1件だけ残して削除する */
    private function removeDuplicateReports(string $column): void
    {
        $duplicateIds = DB::table('reports')
            ->select(DB::raw('min(id) as keep_id'))
            ->whereNotNull('user_id')
            ->whereNotNull($column)
            ->groupBy('user_id', $column)
            ->havingRaw('count(*) > 1')
            ->pluck('keep_id');

        foreach ($duplicateIds as $keepId) {
            $target = DB::table('reports')->find($keepId);

            DB::table('reports')
                ->where('user_id', $target->user_id)
                ->where($column, $target->{$column})
                ->where('id', '!=', $keepId)
                ->delete();
        }
    }
};
