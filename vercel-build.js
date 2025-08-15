const { execSync } = require('child_process');

console.log('Installing PHP dependencies...');
try {
  // Install dependencies
  execSync('composer install --no-dev --optimize-autoloader', { stdio: 'inherit' });
  
  // Generate application key
  execSync('php artisan key:generate --force', { stdio: 'inherit' });
  
  // Cache configuration
  execSync('php artisan config:cache', { stdio: 'inherit' });
  execSync('php artisan route:cache', { stdio: 'inherit' });
  execSync('php artisan view:cache', { stdio: 'inherit' });
  
  // Build assets
  execSync('npm install && npm run production', { stdio: 'inherit' });
  
  console.log('Build completed successfully!');
} catch (error) {
  console.error('Build failed:', error);
  process.exit(1);
}
