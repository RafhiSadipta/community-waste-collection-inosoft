FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libxml2-dev \
        libssl-dev \
        libonig-dev \
        pkg-config \
    && docker-php-ext-install bcmath pdo mbstring xml zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
