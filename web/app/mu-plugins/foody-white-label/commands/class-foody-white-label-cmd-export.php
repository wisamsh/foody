<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/15/19
 * Time: 2:56 PM
 */

if (defined('WP_CLI') && WP_CLI) {

    class Foody_WhiteLabelCmdExport
    {


        public function __construct()
        {
        }

        public function export()
        {

            // give output
            WP_CLI::success('hello from foody_export() !');

        }
    }

    WP_CLI::add_command('foody', 'Foody_WhiteLabelCmdExport');

}