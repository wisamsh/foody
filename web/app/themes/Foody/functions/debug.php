<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/17/19
 * Time: 4:25 PM
 */

add_action('admin_init', 'foody_debug_admin_actions');

function foody_debug_admin_actions()
{
    if (is_multisite() && is_main_site()) {
        try {
            new Foody_WhiteLabelDuplicatorTask();
//            function do_this_in_ten_seconds()
//            {
//                $max_execution_time = ini_get('max_execution_time');
//                ini_set('max_execution_time', 300);
//                Foody_WhiteLabelDuplicator::whiteLabelCreate(2);
//                ini_set('max_execution_time', $max_execution_time);
//            }
//
//            add_action('my_debug_event', 'do_this_in_ten_seconds');
//
//            $scheduled = wp_schedule_single_event(time(), 'my_debug_event');
//            $message = 'scheduled import cron';
//            if ($scheduled === false) {
//                $message = 'schedule import cron failed';
//            }
//
//            Foody_WhiteLabelLogger::info($message);

        } catch (Exception $e) {
            Foody_WhiteLabelLogger::error($e->getMessage(), ['error' => $e]);
        }
    }
}