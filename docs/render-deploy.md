# Render.com デプロイ手順

Laravel 13 + Vite 製の本アプリを Render.com にデプロイするための手順書。

## 構成ファイル

| ファイル | 役割 |
| --- | --- |
| `Dockerfile` | PHP 8.3 + Node 20 でアプリをビルドするイメージ定義 |
| `docker/start.sh` | 起動時に migrate / storage:link / config・view キャッシュ を実行してから `php artisan serve` |
| `render.yaml` | Web サービス（Docker）と Postgres を定義した Blueprint |
| `.dockerignore` | イメージに含めないファイル |

## デプロイ手順

1. リポジトリを GitHub に push する。
2. Render ダッシュボードで **New > Blueprint** を選び、本リポジトリを指定する。
   `render.yaml` に沿って Web サービス `gakuseisien` と Postgres `gakuseisien-db` が作成される。
3. 初回デプロイ後、以下の環境変数を **手動で** 設定する（`render.yaml` で `sync: false` にしてある項目）。
   - `APP_KEY` … ローカルで `php artisan key:generate --show` を実行して得た `base64:...` の値を貼り付ける。
   - `APP_URL` … 割り当てられた本番URL（例 `https://gakuseisien.onrender.com`）。
   - `GNEWS_API_KEY` … 時事ニュース機能を使う場合のみ（https://gnews.io）。
   - `GOOGLE_MAPS_SERVER_KEY` / `GOOGLE_MAPS_EMBED_KEY` … 店舗地図を使う場合のみ。
4. 環境変数を保存すると再デプロイが走り、`start.sh` が migrate を自動実行する。

## 注意点

### アップロード画像は無料プランでは永続化されない
掲示板・学内Q&Aの画像は `storage/app/public` に保存されるが、Render の無料プランはファイルシステムが**一時的**で、再デプロイ・再起動のたびに消える。永続化する場合は次のいずれか:

- `render.yaml` の `disk:` 定義（コメントアウト済み）を有効化して有料の永続ディスクを使う。
- Amazon S3 等の外部ストレージに切り替える（`.env` の `AWS_*` と `FILESYSTEM_DISK=s3`）。

### route:cache は使っていない
`routes/web.php` にクロージャルート（`/`, `/recentnews`, `/notification` など）があるため `route:cache` は失敗する。`start.sh` では `config:cache` と `view:cache` のみ実行している。

### デモアカウント
`php artisan db:seed` で動作確認用の学生・教師アカウントが作成される（`database/seeders/DatabaseSeeder.php` 参照）。本番公開時は不要なら実行しないこと。
