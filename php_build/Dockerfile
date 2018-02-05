FROM php:5.6-fpm-alpine

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apk add --no-cache zlib-dev libuuid util-linux-dev ${PHPIZE_DEPS} && \
    pecl install memcache && \
    pecl install uuid && \
    docker-php-ext-enable memcache && \
    docker-php-ext-enable uuid && \
    apk del zlib-dev util-linux-dev ${PHPIZE_DEPS}
