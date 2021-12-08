FROM php:7.4-apache

# apacheの設定
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
&& sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
&& a2enmod rewrite

# 前準備として各種APIをインストール。
RUN apt-get update && apt-get install -y \
    libicu-dev \
    zip \
    unzip \
    libpq-dev \
&& docker-php-ext-install -j$(nproc) intl \
&& docker-php-ext-install -j$(nproc) pdo_pgsql

# mod_rewriteを 有効に
# /etc/apache2/mods-available/にあるrewrite.loadを/etc/apache2/mods-enabledディレクトリに移動する
# 参考：https://qiita.com/dokkoisho/items/03746e58d975bd7a35ec
RUN  mv /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled \
 && /bin/sh -c a2enmod rewrite

# cakephpをインストール
WORKDIR /var/www
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php \
 && php -r "unlink('composer-setup.php');" \
 && php composer.phar self-update 1.10.10 \
 && php composer.phar create-project --prefer-dist cakephp/app:4.1.0 html


# プラグインをインストール
WORKDIR /var/www/html

#SNSログイン用
RUN php ../composer.phar require firebase/php-jwt:~5.2 \
 && php ../composer.phar require hybridauth/hybridauth:~3.3

 # AWS SDK for PHPをインストール
RUN php ../composer.phar require aws/aws-sdk-php

