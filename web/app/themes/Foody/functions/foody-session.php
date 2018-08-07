<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/25/18
 * Time: 2:52 PM
 */



/*
 * User Favorites
 * */
global $wp_session;
$user_favorites = get_user_meta(get_current_user_id(), 'favorites', true);
$wp_session['favorites'] = $user_favorites;