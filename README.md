# [hng_boilerplate_php_web] Integration Documentation

## Setting Up the Environment
This guide provides step-by-step instructions to help any developer, regardless of their familiarity with Laravel, run the app and test the feature implemented in the PR.

- You should have WSL installed on your windows PC or use a linux distribution(Recommended but not compulsory).
- Have Git installed
- Have PHP 8.1 or higher, PostgreSQL 15 or higher and Composer installed(Mandatory)
- Have Docker installed(Recommended but not compulsory).
- If not using docker, install WAMP, XAMPP or Laragon
## Cloning the repository
git clone https://github.com/hngprojects/hng_boilerplate_php_laravel_web.git
cd hng_boilerplate_php_laravel_web
git checkout scaffold
## Setting up using docker
Set Up Environment Variables
Copy the example environment file to create your own .env file in the project folder:
cp .env.example .env
Open the .env file in a text editor and update the following variables:
- DB_CONNECTION (e.g., pgsql)
- DB_HOST (e.g., 127.0.0.1)
- DB_PORT (e.g., 5432)
- DB_DATABASE (your database name)
- DB_USERNAME (your database username)
- DB_PASSWORD (your database password

## Generate Application Key
Run the following command to generate a new application key:
`sail artisan key:generate or php artisan key:generate`

Run the following command to generate a new JWT secret key:
`sail artisan jwt:secret or php artisan jwt:secret`

## Start the Development Server
Run composer install
Run sail artisan migrate --seed to migrate the tables and seed them, in another terminal.
Run the following command to start the Laravel development server:
sail artisan up
The application will be available at http://127.0.0.1/ or http://localhost/.

## Setting up without docker
Move your project folder into the www folder in WAMP, XAMPP or Laragon

## Set Up Environment Variables
Copy the example environment file to create your own .env file in the project folder:
cp .env.example .env
Run the following command to generate a new JWT secret key:
sail artisan jwt:secret or php artisan jwt:secret 

Open the .env file in a text editor and update the following variables:
- DB_CONNECTION (e.g., pgsql)
- DB_HOST (e.g., pgsgl)
- DB_PORT (e.g., 5432)
- DB_DATABASE (your database name)
- DB_USERNAME (your database username)
- DB_PASSWORD (your database password

## Generate Application Key
Run the following command to generate a new application key:
php artisan key:generate

## Start the Development Server
Run composer install
Run php artisan migrate --seed to migrate the tables and seed them
Run the following command to start the Laravel development server:
php artisan serve
The application will be available at http://127.0.0.1:8000/ or http://localhost/.

