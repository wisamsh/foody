# Foody Migration Tool

Migration tool for conversion of Foody MongoDB database to WordPress environment.
This tool is based on a [Laravel](https://laravel.com) app, so make sure to read the docs.

Requirements:
* A running MongoDB Server
* [PHP MongoDB Driver](http://php.net/manual/en/mongodb.installation.php)

## Usage

Command signature: 
``php artisan foody {action} {option[s]}``

###First run:

* execute the mongo shell commands
found under app/Mongo/scripts.js
* run ``composer install``
* Laravel and WordPress both declare the __() function
for localization, and are conflicting when loaded together.
To solve this you need to manually edit Laravel's function:
    * `sudo vim vendor/laravel/framework/src/Illuminate/Foundation/helpers.php -c ":821"`
    * edit the function declaration and the function_exists line.
    * create relevant directories:
        * mkdir tmp
        * mkdir logs

####Default action:
````
php artisan foody migrate-full
````
####Available action:
````
php artisan foody migrate
````

####Only taxonomy:
````
php artisan foody migrate --taxonomy
````
_Imports all taxonomy types._
_To import a single type use the relevant option._

####Ingredients:
````
php artisan foody migrate --ingredients
````
####Mongo Ingredients:
````
php artisan foody migrate --db-ingredients
````

####Categories:
````
php artisan foody migrate --categories
````
####Pans:
````
php artisan foody migrate --pans
````
####Limitations:
````
php artisan foody migrate --limitations
````
####Accessories:
````
php artisan foody migrate --accessories
````
####Units:
````
php artisan foody migrate --units
````
####Techniques:
````
php artisan foody migrate --techniques
````
####Users:
````
php artisan foody migrate --users
````
####Recipes:
````
php artisan foody migrate --recipes
````
* Note: make sure to import relevant 
all other types before importing recipes
        