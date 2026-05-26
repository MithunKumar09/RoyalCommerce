@echo off
echo Starting RoyalCommerce deployment cache invalidation...

cd /d c:\xampp\htdocs\RoyalCommerce\project

echo Clearing application cache...
php artisan cache:clear

echo Clearing configuration cache...
php artisan config:clear

echo Clearing route cache...
php artisan route:clear

echo Clearing view cache...
php artisan view:clear

echo Clearing compiled classes...
php artisan clear-compiled

echo Deployment cache invalidation complete.
echo Please restart Apache if OPcache is enabled.
pause