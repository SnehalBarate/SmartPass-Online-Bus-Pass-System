FROM php:8.2-apache

RUN docker-php-ext-install mysqli

COPY . /var/www/html/

RUN a2enmod rewrite

RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Increase PHP upload limits
RUN echo "upload_max_filesize = 32M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 32M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_file_uploads = 10" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80
