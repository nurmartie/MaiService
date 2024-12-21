#!/bin/bash

php artisan cache:clear

php artisan key:generate

php artisan migrate

exec "$@"