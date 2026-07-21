#!/bin/sh
set -e

echo "Waiting for MongoDB..."

i=0
until php artisan migrate --force; do
    i=$((i + 1))
    if [ $i -ge 30 ]; then
        echo "Could not connect to MongoDB after 30 attempts. Exiting."
        exit 1
    fi
    echo "MongoDB not ready yet, retrying ($i/30)..."
    sleep 2
done

echo "Seeding database..."
php artisan db:seed --force

echo "Starting Laravel server..."
exec "$@"
