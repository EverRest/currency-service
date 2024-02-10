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

# Install Redis extension using PECL
RUN apk --no-cache add \
    build-base \
    git \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del build-base git

# Copy Supervisor worker configuration
COPY supervisord.conf /etc/supervisor/supervisord.conf

# Install Supervisor
RUN apk --no-cache add supervisor

# Create the log directory for Supervisor
RUN mkdir -p /var/log/supervisor

EXPOSE 9000

