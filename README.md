Base introductory note:

This is a simple demo crud project, build with Laravel.
I used Tailwind for presentation.
This demo includes datatables, charts fed with database data, laravel components, and Database transactions.

Instructions to run the project for the first time:

Clone the project to your work directory: git clone https://github.com/M-Araujo/laravel_crud_with_tables.git
After cloning, enter the project and run command composer install
Run npm install

Then open the project in your editor, make sure you have the env example, copy, and create a new env file
On the env file, only for local purposes make sure your APP_URL is correctly filled (because of the image path) ex:  "
APP_URL=http://localhost:8000"

Fill you BD env, with your database name and credentials.
And then run php artisan migrate (if you don´t have a database already, the command prompt will ask you if you want to
create one).

After that, enter the command line "php artisan tinker" and run the commands bellow, feel free to change the quantities.
User::factory()->count(10)->create();
Colour::factory()->count(10)->create();
Country::factory()->count(10)->create();

You can exit tinker, and run the seeds:

php artisan db:seed --class=UserColoursSeeder
php artisan db:seed --class=UserCountriesSeeder

php artisan storage:link
php artisan key:generate

Php artisan serve to run the project.

Npm run dev in another terminal.

And the setup of the project is done, it should be running with no problems.