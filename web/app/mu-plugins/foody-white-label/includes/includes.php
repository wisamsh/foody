<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/14/19
 * Time: 3:42 PM
 */

require_once plugin_dir_path(__FILE__) . '/class-wp-async-task.php';
require_once plugin_dir_path(__FILE__) . '/class-foody-white-label-duplicator.php';
require_once plugin_dir_path(__FILE__) . '/class-foody-white-label-duplicator-task.php';
require_once plugin_dir_path(__FILE__) . '/class-foody-white-label-logger.php';

require_once PLUGIN_DIR . 'foody-importer/foody-importer.php';
require_once PLUGIN_DIR . 'foody-importer/foody-export.php';
require_once PLUGIN_DIR . 'commands/includes.php';


Foody_WhiteLabelLogger::init();

global $foody_logger;

/** @noinspection PhpUnhandledExceptionInspection */
$foody_logger = new Foody_WhiteLabelLogger();