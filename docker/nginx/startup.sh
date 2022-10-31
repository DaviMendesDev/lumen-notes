#!/usr/bin/env bash

php-fpm8.1

chmod 755 /run/php/php8.1-fpm.sock
chown nginx:nginx /run/php/php8.1-fpm.sock

echo "Whata fuck bro"
