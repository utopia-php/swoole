FROM composer:2.0 as step0

WORKDIR /src/

COPY ./composer.json /src/
COPY ./composer.lock /src/

RUN composer install --ignore-platform-reqs --optimize-autoloader \
    --no-plugins --no-scripts --prefer-dist

FROM php:8.0.18-cli-alpine3.15 as step1

ENV PHP_SWOOLE_VERSION=v5.0.0

RUN \
  apk add --no-cache --virtual .deps \
  make \
  automake \
  autoconf \
  gcc \
  g++ \
  git \
  zlib-dev

RUN \
  ## Swoole Extension
  git clone --depth 1 --branch $PHP_SWOOLE_VERSION https://github.com/swoole/swoole-src.git && \
  cd swoole-src && \
  phpize && \
  ./configure --enable-http2 && \
  make && make install && \
  cd ..

FROM php:8.0-cli-alpine as final

LABEL maintainer="team@appwrite.io"

RUN \
  apk update \
  && apk add --no-cache --virtual .deps \
  gcc \
  g++ \
  && apk add --no-cache \
  libstdc++ \
  && apk del .deps \
  && rm -rf /var/cache/apk/*

WORKDIR /code

COPY --from=step0 /src/vendor /code/vendor
COPY --from=step1 /usr/local/lib/php/extensions/no-debug-non-zts-20200930/swoole.so /usr/local/lib/php/extensions/no-debug-non-zts-20200930/ 

# Add Source Code
COPY ./src /code/src
COPY ./phpunit.xml /code/
COPY ./psalm.xml /code/


# Enable Extensions
RUN echo extension=swoole.so >> /usr/local/etc/php/conf.d/swoole.ini

EXPOSE 80

CMD [ "php", "tests/e2e/server.php"]