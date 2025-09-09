FROM php:8.3-fpm

RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && install-php-extensions decimal opcache pdo_pgsql pdo_mysql pcntl mysqli bcmath intl zip

WORKDIR /app