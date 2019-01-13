<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/29/18
 * Time: 3:54 PM
 */

$subscriber = get_role('subscriber');

add_role('foody_fut_user', "משתמש פיילוט", $subscriber->capabilities);