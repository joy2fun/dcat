#!/usr/bin/env bash

cd ./laravel-tests
php artisan admin:publish --force
cp -f ./.env.testing ./laravel-tests/.env
php artisan admin:install
php artisan migrate:rollback
# php artisan dusk:chrome-driver 109
cp -f ./tests/routes.php ./app/Admin/
cp -rf ./tests/resources/config ./config/
