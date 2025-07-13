#!/bin/sh
set -e

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/.env
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chmod 664 /var/www/.env

exec "$@"