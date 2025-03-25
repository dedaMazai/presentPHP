#!/bin/bash
set -e
set -u

php artisan storage:link
php artisan view:cache
php artisan config:cache
php artisan migrate --force --isolated
supervisord --configuration /etc/supervisor.d/laravel.conf
