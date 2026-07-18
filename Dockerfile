# ------------------------------------------------------------
# gakuseisien（Laravel 13 + Vite）Render.com 向け Dockerfile
# ------------------------------------------------------------
FROM php:8.3-cli

# --- システム依存パッケージ + PHP拡張（PostgreSQL / GD / zip） ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        ca-certificates \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Node.js 20（Viteのフロントエンドビルド用） ---
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# --- 依存インストール（レイヤーキャッシュを効かせるため先にコピー） ---
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY package.json package-lock.json ./
RUN npm ci

# --- アプリ本体 ---
COPY . .

# Composerのオートロード生成 + Viteビルド
RUN composer dump-autoload --optimize --no-dev \
    && npm run build \
    && rm -rf node_modules

# storage / cache を書き込み可能に
RUN chmod -R 775 storage bootstrap/cache

# --- 起動スクリプト ---
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Renderは $PORT を注入する（既定 8080）
ENV PORT=8080
EXPOSE 8080

CMD ["start.sh"]
