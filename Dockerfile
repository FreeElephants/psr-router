ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-cli

# Composer requirements begin
RUN apt-get update \
    && apt-get install -y \
    libzip-dev \
    unzip

RUN docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Composer requirements end

# Xdebug extention and configuration
RUN pecl channel-update pecl.php.net \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.mode=coverage\n" \
    >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


# Prepare image filesystem begin
WORKDIR /var/www
# Prepare image filesystem end
