#!/bin/bash

# Exit on error
set -e

echo "===== Starting build process ====="

# Create required directories
echo "Creating required directories..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Set up environment
if [ ! -f ".env" ]; then
    echo "Copying .env.example to .env..."
    cp .env.example .env
fi

echo "Generating application key..."
php artisan key:generate --force

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm install --production=false

# Build assets
echo "Building assets..."
npm run production

# Create a simple PHP server configuration for Vercel
echo "Creating Vercel server configuration..."
cat > api/index.php << 'EOL'
<?php
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
EOL

echo "===== Build completed successfully! ====="
