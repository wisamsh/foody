<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/ZariMatan
 * @since      1.0.0
 *
 * @package    Foody_White_Label
 * @subpackage Foody_White_Label/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Foody_White_Label
 * @subpackage Foody_White_Label/includes
 * @author     Matan Zari <zari@moveo.co.il>
 */
class Foody_White_Label_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        if (is_main_site()){
            Foody_WhiteLabelPostMapping::createTable();
        }
    }

}
