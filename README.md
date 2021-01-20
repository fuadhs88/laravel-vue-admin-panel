php artisan migrate:fresh --seed –seeder=PermissionSeeder

laravel passport:install

git clone git@github.com:valeriooz/admin140121.git
composer install
npm install
php artisan key:generate
Creare il database mysql
Copiare .env da .env.example
Inserire i dati del database, ip, porta, user e password sull .env
php artisan migrate:fresh --seed –seeder=PermissionSeeder
php artisan passport:install
php artisan serve
npm run watch
php artisan queue:listen
