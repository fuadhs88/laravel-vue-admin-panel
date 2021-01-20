[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/aff1424de760839b69ec)

* `git clone git@github.com:valeriooz/admin140121.git`
* `composer install`
* `npm install`
* Copiare .env da .env.example
* `php artisan key:generate`
* Copiare la chiave generata su .env, APP_KEY
* Creare il database mysql
* Inserire i dati del database, ip, porta, user e password sull .env
* `php artisan migrate:fresh --seed --seeder=PermissionSeeder`
* `php artisan passport:install --force`
* `php artisan serve`
* `npm run watch`
* `php artisan queue:work`

(Se usi una porta diversa da 8000 cambiare la variable port sulla collection postman)
