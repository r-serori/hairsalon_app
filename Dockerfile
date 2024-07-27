# PHP のベースイメージ
FROM php:8.2-apache

# 必要な PHP 拡張とユーティリティをインストール
RUN apt-get update && apt-get install -y \
  libzip-dev \
  unzip \
  git \
  && docker-php-ext-install zip pdo pdo_mysql

# Composer をインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# アプリケーションのコードをコピー
COPY . .

# Composer のキャッシュをクリアして依存関係をインストール
RUN composer clear-cache && composer install --no-interaction --prefer-dist --optimize-autoloader

# パーミッションの設定（必要に応じて）
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
