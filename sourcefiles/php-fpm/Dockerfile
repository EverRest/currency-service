FROM php:8.2-fpm-alpine
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apk --no-cache add \
    postgresql-dev \
    libzip-dev \
    libpng \
    libpng-dev \
    oniguruma-dev \
    freetype \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    libwebp \
    libwebp-dev \
    zlib-dev \
    nginx \
    nodejs \
    npm \
    autoconf \
    g++ \
    make \
    pkgconfig

# Install additional PHP extensions
RUN docker-php-ext-install pdo_pgsql zip exif gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install npm packages
RUN apk --no-cache add \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    zlib-dev \
    && npm install -g npm

# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*