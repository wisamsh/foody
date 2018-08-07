<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/24/18
 * Time: 12:48 PM
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Recipe extends Eloquent
{
    protected $collection = 'recipemodels';

}