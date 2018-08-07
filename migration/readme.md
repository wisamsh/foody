# Foody Migration Tool

Migration tool for conversion of Foody MongoDB database to WordPress environment.
This tool is based on a [Laravel](https://laravel.com) app, so make sure to read the docs.

Requirements:
* A running MongoDB Server
* [PHP MongoDB Driver](http://php.net/manual/en/mongodb.installation.php)

## Usage

###First run:

execute the mongo shell commands
found under app/Mongo/scripts.js

####Default action:
````
php artisan foody migrate
````
####Without taxonomy (categories):
````
php artisan foody migrate --without-taxonomy
````
####Only taxonomy:
````
php artisan foody migrate --only-taxonomy
````
####Ingredients:
````
php artisan foody migrate --ingredients
````

####Sincle record ID
````
php artisan foody migrate --single=1000
````
_Only applies to articles_
