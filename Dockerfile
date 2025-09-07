FROM composer:2.0 as step0

WORKDIR /src/

COPY ./composer.json /src/
COPY ./composer.lock /src/

RUN composer install --ignore-platform-reqs --optimize-autoloader \
    --no-plugins --no-scripts --prefer-dist

FROM appwrite/base:0.10.4 AS final

LABEL maintainer="team@appwrite.io"

WORKDIR /code

COPY --from=step0 /src/vendor /code/vendor

# Add Source Code
COPY ./src /code/src
COPY ./phpunit.xml /code/

EXPOSE 80

CMD [ "php", "tests/e2e/server.php"]