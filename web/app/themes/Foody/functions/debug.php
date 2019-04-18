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
        new Foody_WhiteLabelDuplicatorTask();
        //Foody_WhiteLabelDuplicator::duplicateCategory(6, 2);
    }
}