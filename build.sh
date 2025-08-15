#!/bin/bash

# Exit on error
set -e

echo "Starting build process..."

# Create required directories
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key
echo "Generating application key..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
php artisan key:generate --force

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm install

# Build assets
echo "Building assets..."
npm run production

echo "Build completed successfully!"
