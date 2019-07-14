<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/26/19
 * Time: 4:53 PM
 */

namespace FoodyAPI;


abstract class Foody_BaseAPIController
{
    protected $namespace = 'foody-api';

    protected abstract function getBaseRoute();

    /**
     * @param $request \WP_REST_Request
     * @return bool
     */
    protected function verifyUser($request)
    {
        return current_user_can('access_foody_api');
    }

    protected function registerRoute($endpoint, $args)
    {
        $base = $this->getBaseRoute();
        $route = "/$base/$endpoint";
        register_rest_route($this->namespace, $route, $args);
    }

    public abstract function registerRoutes();

    /**
     * Validates all items in array are
     * alphanumeric strings
     * @param $arr array
     * @return bool
     */
    public function validateAlphaNumericArray($arr)
    {
        $valid = false;
        if (is_array($arr)) {
            $count = count($arr);
            $alpha_numeric_items = array_filter($arr, function ($value) {
                return preg_match('/\p{L}/u', $value);
            });
            $valid = $count === count($alpha_numeric_items);
        }

        return $valid;
    }
}