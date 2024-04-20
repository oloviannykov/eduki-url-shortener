## Project summary

This is a **PHP Link Shortener** - test task to demonstrate my skills.
It accepts regular (long) URLs, store them, and return a shorter link that redirects to the original URL when accessed.

## Task summary

Develop a simple link shortening service using PHP.
- The service should accept regular (long) URLs, store them, and return a shorter link that redirects to the original URL when accessed. Aim to make the shortened links as short as possible.
- Invest minimum in frontend, you can also just do pure API, with no frontend at all.
We would look at your PHP code.
- Choose a suitable PHP framework (Laravel, Symfony, Slim, etc.) for the project or proceed with vanilla PHP if preferred.
- Set up a MySQL database for storing original and shortened links. You can also use other suitable storage.

### Functionality expectation

- Implement an endpoint or form where users can submit URLs to be shortened. Validate the
input to ensure it's a valid URL.
- Develop an algorithm to generate a unique, short code for each URL. This could involve hashing
and encoding, you have to ensure the result is as short as possible while remaining unique.
- When a user accesses a shortened URL, redirect them to the original URL. Implement
appropriate HTTP status codes for successful redirections and handling of not-found cases.
- Save the original URL and its corresponding short code in the database (or storage of your
choice).
- Optional: add counter to track how many times each shortened URL is accessed.

## Implementaion

It consists of three parts:
- Back-End: Laravel framework with JSON API, phpUnit for testing, Blade for page templating
- Front-End: jQuery and Bootstrap installed via NPM, JSON requests to the API
- Database: MySQL

### System requirements
To build and run this project on your computer you need:
- [Git VCS 2.42](https://git-scm.com/downloads) or later
- [PHP 8.2](https://www.php.net/downloads.php) or later
- [MySQL 15.1](https://dev.mysql.com/downloads/) or later
- [Composer 2.7 or later](https://getcomposer.org/download/)
- [Node.js 20.12 or later and NPM 10.5 or later](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm)

## Setting up developer environment

1. Goto directory where you want to store source code and clone the repository:
```shell
git clone https://github.com/oloviannykov/eduki-url-shortener
```

2. Create MySQL database. Instructions [here](https://www.mysqltutorial.org/mysql-basics/mysql-create-database/)

3. Copy **/.env.example** file to **/.env**. Set the MySQL connection in the file:
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=(DB name here)
DB_USERNAME=(DB user name here)
DB_PASSWORD=(DB password here)
```

4. Install PHP dependancies via Composer:
```shell
composer install
```
or using composer.phar
```shell
php composer.phar install
```

5. Install Node.js dependancies via NPM:
```shell
npm install
```

6. Run Laravel migrations:
```shell
php artisan migrate
```

7. Build Front-End:
```shell
npm run build
```

## Test from CLI


## Test from web-browser

1. Start Laravel server:
```shell
php artisan serve
```

2. Open the link in web-browser. You should see the form for adding URL.

3. Insert for example "https://laravel.com/docs/10.x/controllers#singleton-resource-controllers".
Press button "Go". You should see the short link in message below and see other links in 'Recent URLs'.
