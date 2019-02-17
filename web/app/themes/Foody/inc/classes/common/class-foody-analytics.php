<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/14/18
 * Time: 6:42 PM
 */
class Foody_Analytics
{


    private static $instance;
    private $mixpanel;

    /**
     * Foody_Analytics constructor.
     */
    private function __construct()
    {
        $this->mixpanel = Mixpanel::getInstance(MIXPANEL_TOKEN);
    }

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Foody_Analytics();
        }

        return self::$instance;
    }


    public function alias()
    {
        if (!is_user_logged_in()) {
            return;
        }


    }

    public function event($name, $properties = [])
    {
        $this->mixpanel->track($name, $properties);


    }

    public function user_register()
    {
        $user = wp_get_current_user();
        $this->event('register', [
            'email' => $user->user_email,
            'id' => $user->ID,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        ]);

    }
}