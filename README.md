## About E-COMMERCE-BACKEND-APP


After cloning the project:

- run composer install --ignore-platform-reqs
- run cp .env.example .env
- run php artisan key:generate
- run php artisan storage:link
- Manually Create The Database and Add the DB Name , Connection , Host , Port , username and Password in .env
- run php artisan migrate
- run php artisan db:seed --class=DatabaseSeeder
- run php artisan serve.

The Project is Ready