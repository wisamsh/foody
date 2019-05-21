<?php
/**
 * Template Name: Page Redirect
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
get_header();
header( 'Location: ' . $_GET['redirect_to'] );

exit();