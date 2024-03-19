FROM php:8.3-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libxslt-dev \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable intl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && apt-get install -y libxml2-dev \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install xsl \
    && docker-php-ext-enable sodium

ARG WITH_XDEBUG=true
ARG XDEBUG_VERSION=3.1.3
RUN if [ "$WITH_XDEBUG" = "true" ] ; then \
    pecl install xdebug-$XDEBUG_VERSION; \
    docker-php-ext-enable xdebug; \
    echo "max_execution_time=900" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
fi;

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app