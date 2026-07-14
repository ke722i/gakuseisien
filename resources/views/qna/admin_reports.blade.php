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
</head>

<body>
    <main class="content qna-page">

        <div class="qna-header-container">
            <h1 class="qna-title" style="color: #dc3545;">🚨 通報管理一覧（教職員専用）</h1>
            <a href="{{ route('gakunai.qna') }}" class="qna-history-btn">Q&A一覧に戻る</a>
        </div>

        <div class="app-layout">
            @include('partials.sidebar', ['active' => 'qna'])
        </div>

        <div class="qna-card-list">
            
            @forelse ($reports as $report)
            <article class="qna-custom-card" style="border-left: 5px solid #dc3545; margin-bottom: 15px;">
                <div class="qna-card-body">
                    <div class="qna-card-text" style="width: 100%;">
                        
                        <div style="margin-bottom: 8px; font-size: 13px; color: #666666;">
                            <span class="qna-badge" style="background-color: #dc3545; color: #ffffff; padding: 2px 8px; border-radius: 4px; margin-right: 8px;">
                                通報ID: {{ $report->id }}
                            </span>
                            <span>受信日時: {{ $report->created_at->format('Y/m/d H:i') }}</span>
                        </div>

                        <div style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 12px; border: 1px solid #e9ecef;">
                            <p style="margin: 0; font-size: 12px; color: #6c757d; font-weight: bold;">対象の質問タイトル：</p>
                            <h3 style="margin: 5px 0 0 0; font-size: 16px;">
                                @if($report->question)
                                    {{ $report->question->title }}
                                @else
                                    <span style="color: #999; font-style: italic;">（既に削除された質問です）</span>
                                @endif
                            </h3>
                        </div>

                        <div>
                            <p style="margin: 0; font-size: 12px; color: #6c757d; font-weight: bold;">⚠️ 通報された理由：</p>
                            <p style="margin: 5px 0 0 0; font-size: 15px; color: #333333; white-space: pre-wrap; font-weight: bold;">{{ $report->reason }}</p>
                        </div>

                    </div>
                </div>

                <div class="qna-card-footer" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                    @if($report->question)
                        <a href="{{ route('qna.detail', $report->question_id) }}" class="qna-read-more" style="color: #007bff;">
                            対象の質問詳細を確認する ➡️
                        </a>
                    @endif
                </div>
            </article>
            @empty
            <div class="qna-custom-card" style="text-align: center; padding: 40px; color: #666666;">
                <p style="font-size: 16px; font-weight: bold; color: #28a745;">現在、通報されている投稿はありません。</p>
                <p style="font-size: 14px; margin-top: 5px;">平和な状態が保たれています。✨</p>
            </div>
            @endforelse

        </div>
    </main>
</body>

</html>