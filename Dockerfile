FROM php:8.2-apache

RUN docker-php-ext-install mysqli

COPY . /var/www/html/

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html/uploads && chmod -R 775 /var/www/html/uploads

EXPOSE 80
