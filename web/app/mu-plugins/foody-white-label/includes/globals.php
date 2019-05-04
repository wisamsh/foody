<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/29/19
 * Time: 4:37 PM
 */

global $foody_auto_synced_post_types;
$foody_auto_synced_post_types = [
    'foody_ingredient',
    'foody_technique',
    'foody_accessory'
];


global $foody_auto_synced_taxonomies;
$foody_auto_synced_taxonomies = [
    'pans',
    'units',
    'limitations'
];

global /** @var Foody_WhiteLabelTermDuplicatorProcess $term_duplicator_process */
$term_duplicator_process;