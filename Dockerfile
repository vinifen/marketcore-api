FROM php:8.3-fpm-alpine

# Instala dependências necessárias para Laravel com PostgreSQL
RUN apk update && apk add --no-cache \
    curl \
    libxml2-dev \
    oniguruma-dev \
    libpq-dev \
    unzip

# Instala somente as extensões obrigatórias
RUN docker-php-ext-install pdo pdo_pgsql mbstring xml

# Adiciona Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Configurações personalizadas do PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]