<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/15/19
 * Time: 2:56 PM
 */

if (defined('WP_CLI') && WP_CLI) {

    class Foody_CLI
    {


        public function __construct()
        {
        }

        public function activate_plugins($args)
        {
            if (!isset($args[0])) {
                WP_CLI::error('no plugins list specified');
                die();
            }

            $data = file_get_contents($args[0]);

            $plugins = json_decode($data);

            $plugins = array_filter($plugins, function ($plugin) {
                return in_array($plugin['status'], ['active', 'active-network']);
            });

            $results = [];
            foreach ($plugins as $plugin) {

                $status = $plugin['status'];
                $name = $plugin['name'];

                $activation_type = $status == 'active-network' ? '--network' : '';
                $cmd = "wp plugin activate $name $activation_type";
                $results[] = exec($cmd);
            }

            WP_CLI::success('plugins successfully activated');
        }

    }

    WP_CLI::add_command('foody-cli', 'Foody_CLI');

}