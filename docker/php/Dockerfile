FROM php:8-fpm-alpine

RUN set -ex \
  && apk add --no-cache --update zlib \
  && apk --no-cache add \
     postgresql-dev \
     autoconf \
     curl \
     make \
     g++
RUN docker-php-ext-install exif bcmath pdo pdo_pgsql \
     && docker-php-ext-configure intl \
     && docker-php-ext-install  intl \
     && docker-php-ext-enable intl

COPY ./php.ini /usr/local/etc/php/conf.d/dev.ini
COPY ./fpm.conf /usr/local/etc/php-fpm.d/x.fpm.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
