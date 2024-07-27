# ベースイメージを指定
FROM php:7.4-fpm

# 必要なPHP拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql

# PHP-FPM を使うために Apache の設定を変更
RUN a2enmod proxy_fcgi
RUN a2enconf php7.4-fpm

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# アプリケーションのソースコードをコピー
COPY . .

# 依存関係をインストール
RUN composer install

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/html

# Laravelのキャッシュをクリア
RUN php artisan cache:clear
RUN php artisan config:clear

# Apache のポートを公開
EXPOSE 80

# Apache を起動
CMD ["apache2-foreground"]

