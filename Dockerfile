# Apache のベースイメージを使用
FROM php:8.1-apache

# PHP-FPM を使うために Apache の設定を変更
RUN apt-get update && apt-get install -y \
  libapache2-mod-fcgid \
  && a2enmod proxy_fcgi \
  && a2enconf php8.1-fpm

# 必要な PHP 拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql

# Composer をインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# アプリケーションのソースコードをコピー
COPY . .

# 依存関係をインストール
RUN composer install

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/html

# Laravel のキャッシュをクリア
RUN php artisan cache:clear
RUN php artisan config:clear
