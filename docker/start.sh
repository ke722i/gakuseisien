#!/usr/bin/env sh
# ------------------------------------------------------------
# Render 起動時に実行される。マイグレーション適用・storageリンク・
# 各種キャッシュを行ってからアプリを起動する。
# ------------------------------------------------------------
set -e

# DBマイグレーション（未適用のものだけ適用。既存データは消さない）
php artisan migrate --force

# storage/app/public を public/storage へリンク（アップロード画像の表示に必要）
php artisan storage:link --force || true

# 本番用キャッシュ（config はキャッシュ、route はクロージャルートがあるため対象外）
php artisan config:cache
php artisan view:cache

# アプリ起動。Render が渡す $PORT を使う。
php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
