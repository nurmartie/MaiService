FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        libpq-dev \
        libzip-dev \
        libicu-dev \
        git \
        curl \
        zip \
        unzip \
        default-mysql-client

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql intl zip

COPY vendor /var/www/MaiService/vendor

COPY docker/app/scripts/entrypoint.sh /var/www/MaiService/docker/scripts/entrypoint.sh
RUN chmod +x /var/www/MaiService/docker/scripts/entrypoint.sh

WORKDIR /var/www/MaiService/

USER 1000:1000

EXPOSE 9000

ENTRYPOINT ["./docker/scripts/entrypoint.sh"]
CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
