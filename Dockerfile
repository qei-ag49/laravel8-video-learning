# 使用するベースイメージを指定
FROM php:7.4-apache

# 必要なパッケージとツールをインストール
RUN apt-get update && apt-get install -y \
  zlib1g-dev libzip-dev libonig-dev unzip libpq-dev vim busybox-static postgresql-client \
  && apt-get clean

# 以下の2行でLaravel用のComposerイメージをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# PHPの拡張モジュールをインストール
# RUN docker-php-ext-install pdo_pgsql zip
RUN docker-php-ext-install pdo pgsql pdo_pgsql zip

# ApacheのUIDとGIDをDockerユーザーのUID/GIDに変更
RUN usermod -u 1000 www-data \
  && groupmod -g 1000 www-data

# Apacheの設定を変更
COPY ./php/vhost.conf /etc/apache2/conf-enabled/vhost.conf

# Apacheのrewriteモジュールを有効化
RUN a2enmod rewrite

# ソースコードと.envファイルをDockerイメージにコピー
ENV APP_HOME /var/www/html
COPY . $APP_HOME
COPY .env.production $APP_HOME/.env

# 初回起動時に実行するスクリプトをコピーして実行権限を与える
COPY ./php/start.sh $APP_HOME/start.sh
RUN chmod 744 $APP_HOME/start.sh

# キャッシュ用のディレクトリを作成
RUN mkdir $APP_HOME/bootstrap/sessions \
  && mkdir $APP_HOME/storage/framework/cache/data

# フレームワークに必要なモジュールをインストール
RUN composer install --no-dev --no-interaction -d $APP_HOME

# 書き込み権限を与える
RUN chown -R www-data:www-data $APP_HOME

# コンテナの起動時に実行されるコマンドを指定
CMD ["bash", "start.sh"]
