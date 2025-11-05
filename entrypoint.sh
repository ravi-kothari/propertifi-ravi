#!/bin/sh

# Exit immediately if a command exits with a non-zero status.
set -e

# Wait for the database to be ready
echo "Waiting for MySQL database..."
MAX_TRIES=30
COUNT=0
until php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null || [ $COUNT -eq $MAX_TRIES ]; do
    echo "Still waiting for MySQL... ($COUNT/$MAX_TRIES)"
    COUNT=$((COUNT + 1))
    sleep 2
done

if [ $COUNT -eq $MAX_TRIES ]; then
    echo "Failed to connect to MySQL after $MAX_TRIES attempts"
    exit 1
fi

echo "MySQL is up and running."

# Run database migrations and seeders
echo "Running database migrations..."
php artisan migrate --force

echo "Running database seeders..."
php artisan db:seed --force

# Start the Laravel development server
echo "Starting Laravel development server on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
