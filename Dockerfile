# ベースイメージを指定
FROM php:7.4-fpm

# 必要なPHP拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www

# アプリケーションのソースコードをコピー
COPY . .

# 依存関係をインストール
RUN composer install

# パーミッションの設定
RUN chown -R www-data:www-data /var/www

# Laravelのキャッシュをクリア
RUN php artisan cache:clear
RUN php artisan config:clear

# アプリケーションを起動
CMD ["php-fpm"]
