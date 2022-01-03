FROM php:8.0.10-fpm-alpine

COPY backend /backend

RUN apk add composer

WORKDIR /backend

CMD ["sh", "-c", "composer install && php-fpm"]
