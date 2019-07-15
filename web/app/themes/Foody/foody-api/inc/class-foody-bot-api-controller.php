<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/26/19
 * Time: 4:50 PM
 */

namespace FoodyAPI;

use WP_REST_Server, WP_REST_Request, WP_REST_Response, WP_Error;

class Foody_BotAPIController extends Foody_BaseAPIController
{
    /*
     * @var $bot_handler Foody_BotHandler
     * */
    private $bot_handler;

    /**
     * Foody_BotAPIController constructor.
     */
    public function __construct()
    {
        $this->bot_handler = new Foody_BotHandler();
    }


    /**
     * Register the routes for the objects of the controller.
     */
    public function registerRoutes()
    {
        $this->registerRoute('query', array(
            array(
                'methods' => WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'query'),
                'permission_callback' => array($this, 'queryPermissionsCheck'),
                'args' => $this->getCommonArgs()
            )
        ));

        $this->registerRoute('results', array(
            array(
                'methods' => WP_REST_Server::ALLMETHODS,
                'callback' => array($this, 'getResults'),
                'permission_callback' => array($this, 'queryPermissionsCheck'),
                'args' => $this->getCommonArgs()
            )
        ));
    }

    private function getCommonArgs()
    {

        $array_params = [
            'ingredients',
            'authors',
            'accessories',
            'tags',
            'techniques',
            'limitations'
        ];


        $args = [
            'context' => [
                'default' => 'view',
                'required' => true,
            ],
            'level' => [
                'description' => 'limit to recipes with a certain difficulty level',
                'type' => 'string',
                'default' => '',
                'validate_callback' => [$this, 'validateLevel']
            ],
            'time' => [
                'description' => 'set minimum and maximum preparation time',
                'type' => 'object',
                'items' => [
                    'min' => [
                        'type' => 'number'
                    ],
                    'max' => [
                        'type' => 'number'
                    ],
                ],
                'default' => [],
                // TODO
//                'validate_callback' => [$this, 'validateTime']
            ]
        ];

        foreach ($array_params as $array_param) {
            $args[$array_param] = $this->getArrayParamArguments("$array_param names to search for");
        }

        return $args;
    }

    private function getArrayParamArguments($description)
    {
        return array(
            'description' => $description,
            'type' => 'array',
            'items' => [
                'type' => 'string'
            ],
            'default' => [],
            'validate_callback' => [$this, 'validateAlphaNumericArray']
        );
    }

    /**
     *
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function query($request)
    {
        $params = $this->getQueryParams($request);
        $results = $this->bot_handler->query($params);

        return new WP_REST_Response($results);
    }

    /**
     *
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getResults($request)
    {
        $params = $this->getQueryParams($request);

        $results = $this->bot_handler->getResults($params);

        return new WP_REST_Response($results);
    }


    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function queryPermissionsCheck($request)
    {
        return $this->verifyUser($request);
    }


    protected function getBaseRoute()
    {
        return 'bot';
    }

    private function getQueryParams(WP_REST_Request $request)
    {
        $json = $request->get_json_params();

        $defaults = [
            'ingredients' => [],
            'tags' => [],
            'accessories' => [],
            'techniques' => [],
            'authors' => [],
            'limitations' => [],
            'level' => '',
            'time' => ''
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($json[$key])) {
                $json[$key] = $default;
            }
        }

        return $json;
    }

    public function validateLevel($level)
    {
        $valid = empty($level);
        if (!$valid) {
            $level_settings = get_field_object('field_5b34fe4c9c7eb');
            $valid = in_array($level, $level_settings['choices']);
        }

        return $valid;
    }

    public function validateTime($time)
    {
        // TODO
        $valid = true;
        if ((!isset($time['min']) || is_numeric($time['min'])) || (!isset($time['max']) || is_numeric($time['max']))) {

            if (isset($time['min']) && isset($time['max'])) {

                $valid = intval($time['min']) < intval($time['max']);
            }
        }


        return $valid;
    }
}