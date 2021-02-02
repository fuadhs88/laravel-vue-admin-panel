User, roles and permissions CRUD with Laravel, MySQL, Vue, using laravel-permissions, vuex, vue-router

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/aff1424de760839b69ec)

-   `git clone git@github.com:valeriooz/admin140121.git`
-   `cd admin140121`
-   `composer install`
-   `npm install`
-   Copiare .env da .env.example
-   `php artisan key:generate`
-   Copiare la chiave generata su .env, APP_KEY
-   Creare il database mysql
-   Inserire i dati del database, ip, porta, user e password sull .env
-   `php artisan migrate:fresh --seed --seeder=PermissionSeeder`
-   `php artisan passport:install --force`
-   `php artisan serve`
-   `npm run dev` or `npm run prod`
-   `php artisan queue:work`

Per resettare:
`php artisan migrate:fresh --seed --seeder=PermissionSeeder && php artisan passport:install --force`
(Ricorda di cancellare il file installed.json su storage)

(Se usi una porta diversa da 8000 cambiare la variable port sulla collection postman)
