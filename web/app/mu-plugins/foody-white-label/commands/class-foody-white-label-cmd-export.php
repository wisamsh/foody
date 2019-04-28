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

        public function import($args, $accos_args)
        {
            $file = $args[0];

            (new Foody_Import())->import($file);

            WP_CLI::success('foody');
        }

        public function test($args)
        {
            $res = get_field_object('ingredients_ingredients_groups_0_ingredients_0_amounts_0_unit', 65);
            WP_CLI::success($args[0]);
        }
    }

    WP_CLI::add_command('foody', 'Foody_WhiteLabelCmdExport');

}