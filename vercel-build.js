// This file is intentionally left empty
// The build process is handled by vercel.json and package.json
const { execSync } = require('child_process');
const fs = require('fs');

console.log('Starting build process...');

try {
  // Create required directories
  console.log('Creating required directories...');
  const dirs = [
    'storage/framework/views',
    'storage/framework/cache',
    'storage/framework/sessions',
    'bootstrap/cache'
  ];
  
  dirs.forEach(dir => {
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
      console.log(`Created directory: ${dir}`);
    }
  });

  // Copy .env.example to .env if .env doesn't exist
  if (!fs.existsSync('.env')) {
    console.log('Copying .env.example to .env...');
    fs.copyFileSync('.env.example', '.env');
  }

  // Install PHP dependencies
  console.log('Installing PHP dependencies...');
  execSync('composer install --no-dev --optimize-autoloader', { stdio: 'inherit' });
  
  // Generate application key
  console.log('Generating application key...');
  execSync('php artisan key:generate --force', { stdio: 'inherit' });
  
  // Cache configuration
  console.log('Caching configuration...');
  execSync('php artisan config:cache', { stdio: 'inherit' });
  execSync('php artisan route:cache', { stdio: 'inherit' });
  execSync('php artisan view:cache', { stdio: 'inherit' });
  
  // Install Node.js dependencies
  console.log('Installing Node.js dependencies...');
  execSync('npm install', { stdio: 'inherit' });
  
  // Build assets
  console.log('Building assets...');
  execSync('npm run production', { stdio: 'inherit' });
  
  console.log('Build completed successfully!');
} catch (error) {
  console.error('Build failed:', error);
  process.exit(1);
}
