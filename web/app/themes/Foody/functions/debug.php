<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/17/19
 * Time: 4:25 PM
 */

add_action('wp_dashboard_setup', 'foody_debug_admin_actions');

function foody_debug_admin_actions()
{
//    if (is_multisite() && is_main_site()) {
//        try {
//            new Foody_WhiteLabelDuplicatorTask();
//        } catch (Exception $e) {
//            Foody_WhiteLabelLogger::error($e->getMessage(),['error'=>$e]);
//        }
//    }

    if (is_multisite() && is_main_site()) {
        $max_execution_time = ini_get('max_execution_time');
        ini_set('max_execution_time', 300);
        Foody_WhiteLabelDuplicator::whiteLabelCreate(2);
        ini_set('max_execution_time', $max_execution_time);
    }
}