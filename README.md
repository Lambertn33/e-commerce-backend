## About NIDA DMS(DOCUMENTS MANAGEMENT SYSTEM)

NIDA DMS Is a project Which helps NIDA(National Identification Agency) To Keep their files in a digital way..

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