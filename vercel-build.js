const { execSync } = require('child_process');
const fs = require('fs');

console.log('Starting build process...');

try {
  // Create required directories if they don't exist
  if (!fs.existsSync('storage/framework/views')) {
    fs.mkdirSync('storage/framework/views', { recursive: true });
  }
  if (!fs.existsSync('storage/framework/cache')) {
    fs.mkdirSync('storage/framework/cache', { recursive: true });
  }
  if (!fs.existsSync('storage/framework/sessions')) {
    fs.mkdirSync('storage/framework/sessions', { recursive: true });
  }
  if (!fs.existsSync('bootstrap/cache')) {
    fs.mkdirSync('bootstrap/cache', { recursive: true });
  }

  console.log('Installing PHP dependencies...');
  execSync('composer install --no-dev --optimize-autoloader', { stdio: 'inherit' });
  
  console.log('Generating application key...');
  if (!fs.existsSync('.env')) {
    fs.copyFileSync('.env.example', '.env');
  }
  execSync('php artisan key:generate --force', { stdio: 'inherit' });
  
  console.log('Caching configuration...');
  execSync('php artisan config:cache', { stdio: 'inherit' });
  execSync('php artisan route:cache', { stdio: 'inherit' });
  execSync('php artisan view:cache', { stdio: 'inherit' });
  
  console.log('Installing Node.js dependencies...');
  execSync('npm install', { stdio: 'inherit' });
  
  console.log('Building assets...');
  execSync('npm run production', { stdio: 'inherit' });
  
  console.log('Build completed successfully!');
} catch (error) {
  console.error('Build failed:', error);
  process.exit(1);
}
