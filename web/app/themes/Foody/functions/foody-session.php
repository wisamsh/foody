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
$user_favorites          = get_user_meta( get_current_user_id(), 'favorites', true );
$wp_session['favorites'] = $user_favorites;


$user_followed_authors          = get_user_meta( get_current_user_id(), 'followed_authors', true );
$wp_session['followed_authors'] = $user_followed_authors;

$user_followed_channels          = get_user_meta( get_current_user_id(), 'followed_channels', true );
$wp_session['followed_channels'] = $user_followed_channels;

$user_followed_feed_channels          = get_user_meta( get_current_user_id(), 'followed_feed_channels', true );
$wp_session['followed_feed_channels'] = $user_followed_feed_channels;