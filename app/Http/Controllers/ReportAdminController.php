<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

/**
 * 教職員向けの通報管理。
 * 掲示板（forum_post）と学内Q&A（question）の通報をまとめて扱う。
 * ルート側で teacher ミドルウェアを掛けている前提。
 */
class ReportAdminController extends Controller
{
    /** 通報一覧（?type=forum_post / question で絞り込み） */
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');
        if (! in_array($type, ['all', 'forum_post', 'question'], true)) {
            $type = 'all';
        }

        $reports = Report::with(['question', 'post', 'user'])
            ->when($type !== 'all', fn ($q) => $q->where('type', $type))
            ->latest()
            ->get();

        // 絞り込みタブに出す件数
        $counts = [
            'all' => Report::count(),
            'forum_post' => Report::where('type', 'forum_post')->count(),
            'question' => Report::where('type', 'question')->count(),
        ];

        return view('admin_reports', [
            'reports' => $reports,
            'type' => $type,
            'counts' => $counts,
            'active' => 'admin_reports',
        ]);
    }

    /**
     * 通報を取り下げる（対応済みとして一覧から消す）。
     * 投稿自体は削除せず、通報レコードのみを削除する。
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return back()->with('success', '通報を処理済みにしました。');
    }
}
