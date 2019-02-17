<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Jenssegers\Mongodb\Connection as MongoDBConnection;

class ExampleTest extends TestCase
{

    public function testCategories()
    {

        $db = new MongoDBConnection([
            'driver' => 'mongodb',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', 27017),
            'database' => 'Foody',
            'username' => '',
            'password' => ''
        ]);

        $query = $db->collection('recipemodels')->select(['General.Category']);

        $categories = $query->get();

        $arr = $categories->toArray();

        $arr = array_map(function ($model) {
            return $model['General']['Category'];
        }, $arr);

        $arr = array_flatten($arr);

        $arr = array_filter($arr, function ($category) {
            return $category != null;
        });
        $arr = array_unique($arr);

        assert(count($arr) == 70);
    }
}
