<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Foody
 */

foody_get_template_part(
    get_template_directory() . '/template-parts/content-content-with-sidebar.php',
    ['hide_progress' => true]
);