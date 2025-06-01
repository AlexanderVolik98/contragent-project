FROM php:8.3-fpm

# Установка системных зависимостей и расширений
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libxslt1-dev \
    zlib1g-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip opcache sockets pcntl bcmath pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Настройка прав
RUN chown -R www-data:www-data /var/www/html
