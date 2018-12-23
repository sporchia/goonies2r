[![Build Status](https://travis-ci.org/sporchia/goonies2r.svg?branch=master)](https://travis-ci.org/sporchia/goonies2r)
[![Coverage Status](https://coveralls.io/repos/github/sporchia/goonies2r/badge.svg?branch=master)](https://coveralls.io/github/sporchia/goonies2r?branch=master)
[![StyleCI](https://github.styleci.io/repos/161082326/shield?branch=master)](https://github.styleci.io/repos/161082326)

# Goonies 2: Randomizer
This is a Graph based Randomizer of Goonies 2. Currently it shuffles items and Goonies, more to come.

## Installing dependencies
You will need [Composer](https://getcomposer.org/) for the Laravel Dependency. Once you have that, run the following

```
$ composer install
```

## [Telescope](https://laravel.com/docs/5.7/telescope) (optional)
If you are planning on doing development, Telescope has proven to be invaluable.

```
$ php artisan telescope:install
$ php artisan migrate
```

## Running from the command line
To generate a game one simply runs the command:

```
$ php artisan randomize {input_file.sfc} {output_directory}
```

For help (and all the options):

```
$ php artisan alttp:randomize -h
```

## Running the Web Interface

### Database setup
Create a new mysql database for the randomizer (see mysql documentation for how to do this, you'll need to install mysql server if it's not installed already)

Run the following command to create a new config for the app
```
$ cp .env.example .env
```

Then modify .env with appropriate username, password, and database name. Change the db connection to mysql
Example:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=randomizer
DB_USERNAME=foo
DB_PASSWORD=bar
```

Then run the following commands to setup the app configuration

```
$ php artisan key:generate
$ php artisan config:cache
```
p.s. If you update the .env file then you'll need to run the config:cache command to pick up the new changes.

Now run the db migration command:

```
$ php artisan migrate
```

### Web server setup
You will need to build assets the first time (you will need [NPM](https://www.npmjs.com/get-npm) to install the javascript dependencies).

```
$ npm install
$ npm run production
```

Once you have the dependencies installed. Run the following command then navigate to http://localhost:8000/.

```
$ php artisan serve
```

## Running tests
You can run the current test suite with the following command (you may need to install [PHPUnit](https://phpunit.de/))

```
$ composer test
$ php artisan code:analyse --level=max
```

## Bug Reports
Bug reports for the current release version can be opened in this repository's [issue tracker](https://github.com/sporchia/goonies2r/issues).

Please do not open issues for bugs that you encounter when testing a development branch.
