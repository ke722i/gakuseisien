<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>【教職員用】通報一覧 - 学生支援.com</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite([
    'resources/css/app.css',
    'resources/css/qna/qna.css'
    ])
    <style>
        .report-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
        .report-tab {
            padding: 8px 16px; border: 1px solid #cbd5e1; border-radius: 8px;
            text-decoration: none; color: #334155; font-weight: 700; font-size: 14px; background: #fff;
        }
        .report-tab.is-active { background: #dc3545; color: #fff; border-color: #dc3545; }
        .report-tab .cnt { opacity: .75; margin-left: 4px; font-weight: 400; }
        .source-badge {
            display: inline-block; padding: 2px 10px; border-radius: 999px;
            font-size: 12px; font-weight: 700; margin-right: 8px;
        }
        .source-forum { background: #dbeafe; color: #1e40af; }
        .source-qna { background: #fef3c7; color: #92400e; }
        .report-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .btn-resolve {
            border: 1px solid #cbd5e1; background: #fff; color: #334155;
            border-radius: 8px; padding: 6px 14px; font-size: 13px; font-weight: 700; cursor: pointer;
        }
        .btn-resolve:hover { border-color: #16a34a; color: #16a34a; }
    </style>
</head>

<body>
    @include('partials.sidebar', ['active' => 'admin_reports'])
    <main class="content qna-page">

        <div class="qna-header-container">
            <h1 class="qna-title" style="color: #dc3545;">🚨 通報管理一覧（教職員専用）</h1>
        </div>

        {{-- 掲示板・Q&A の絞り込み --}}
        <div class="report-tabs">
            <a href="{{ route('adminReports') }}"
               class="report-tab {{ $type === 'all' ? 'is-active' : '' }}">すべて<span class="cnt">{{ $counts['all'] }}</span></a>
            <a href="{{ route('adminReports', ['type' => 'forum_post']) }}"
               class="report-tab {{ $type === 'forum_post' ? 'is-active' : '' }}">掲示板<span class="cnt">{{ $counts['forum_post'] }}</span></a>
            <a href="{{ route('adminReports', ['type' => 'question']) }}"
               class="report-tab {{ $type === 'question' ? 'is-active' : '' }}">学内Q&A<span class="cnt">{{ $counts['question'] }}</span></a>
        </div>

        <div class="qna-card-list">

            @forelse ($reports as $report)
                @php
                    // type が未設定の古いデータでも、紐づくIDから種別を判定する
                    $isForum = $report->type === 'forum_post' || (! $report->type && $report->post_id);
                    $target = $isForum ? $report->post : $report->question;
                @endphp

                <article class="qna-custom-card" style="border-left: 5px solid {{ $isForum ? '#2563eb' : '#e0a800' }}; margin-bottom: 15px;">
                    <div class="qna-card-body">
                        <div class="qna-card-text" style="width: 100%;">

                            <div style="margin-bottom: 8px; font-size: 13px; color: #666666;">
                                <span class="source-badge {{ $isForum ? 'source-forum' : 'source-qna' }}">
                                    {{ $isForum ? '掲示板' : '学内Q&A' }}
                                </span>
                                <span class="qna-badge" style="background-color: #dc3545; color: #ffffff; padding: 2px 8px; border-radius: 4px; margin-right: 8px;">
                                    通報ID: {{ $report->id }}
                                </span>
                                <span>受信日時: {{ $report->created_at->format('Y/m/d H:i') }}</span>
                                @if ($report->user)
                                    <span style="margin-left: 8px;">通報者: {{ $report->user->login_id }}</span>
                                @endif
                            </div>

                            <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 12px; border: 1px solid #e9ecef;">
                                <p style="margin: 0; font-size: 12px; color: #6c757d; font-weight: bold;">
                                    {{ $isForum ? '対象の掲示板投稿：' : '対象の質問タイトル：' }}
                                </p>
                                <h3 style="margin: 5px 0 0 0; font-size: 16px;">
                                    @if ($target)
                                        {{ $target->title }}
                                    @else
                                        <span style="color: #999; font-style: italic;">（既に削除された投稿です）</span>
                                    @endif
                                </h3>
                                @if ($target && $isForum && $target->category)
                                    <p style="margin: 6px 0 0; font-size: 12px; color: #6c757d;">カテゴリ: {{ $target->category }}</p>
                                @endif
                            </div>

                            <div>
                                <p style="margin: 0; font-size: 12px; color: #6c757d; font-weight: bold;">⚠️ 通報された理由：</p>
                                <p style="margin: 5px 0 0 0; font-size: 15px; color: #333333; white-space: pre-wrap; font-weight: bold;">{{ $report->reason }}</p>
                            </div>

                        </div>
                    </div>

                    <div class="qna-card-footer" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                        <div class="report-actions">
                            @if ($target)
                                <a href="{{ $isForum ? route('forum.show', $target->id) : route('qna.detail', $target->id) }}"
                                   class="qna-read-more" style="color: #007bff;">
                                    {{ $isForum ? '対象の投稿を確認する' : '対象の質問詳細を確認する' }} ➡️
                                </a>
                            @endif

                            <form method="POST" action="{{ route('admin.reports.destroy', $report) }}"
                                  onsubmit="return confirm('この通報を処理済みにしますか？（投稿自体は削除されません）');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-resolve">処理済みにする</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="qna-custom-card" style="text-align: center; padding: 40px; color: #666666;">
                    <p style="font-size: 16px; font-weight: bold; color: #28a745;">
                        {{ $type === 'all' ? '現在、通報されている投稿はありません。' : 'この種別の通報はありません。' }}
                    </p>
                    <p style="font-size: 14px; margin-top: 5px;">平和な状態が保たれています。✨</p>
                </div>
            @endforelse

        </div>
    </main>
</body>

</html>
