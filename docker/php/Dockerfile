FROM php:8.1-fpm

COPY docker/php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN apt-get update && apt-get install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev  \
    libonig-dev \
    libxslt1-dev \
    acl \
    python \
    python3-pip \
    fabric \
    libxml2-dev \
    libcurl4-openssl-dev  \
    pkg-config  \
    libssl-dev \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc

RUN docker-php-ext-configure gd --with-jpeg --with-freetype


RUN docker-php-ext-install \
    pdo pdo_mysql zip xsl gd intl opcache exif mbstring soap bcmath

# Install redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN pecl install xdebug
COPY docker/php/90-xdebug.ini "${PHP_INI_DIR}/conf.d/"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www