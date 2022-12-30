#!/usr/bin/env bash

php-fpm8.1

chmod 755 /run/php/php8.1-fpm.sock
chown nginx:nginx /run/php/php8.1-fpm.sock

mkdir -p /var/www/html/storage/
chmod -R 775 /var/www/html/storage/
chown -R www-data:www-data /var/www/html/storage/

cd /var/www/html && composer install
