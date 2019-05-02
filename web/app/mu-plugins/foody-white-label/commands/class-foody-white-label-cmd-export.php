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
            $total = isset( $assoc_args ) && array_key_exists( 'total', $assoc_args ) ? absint( $assoc_args[ 'total' ] ) : 100;
            WP_CLI::line( 'Starting Example' );
            $progress = \WP_CLI\Utils\make_progress_bar( 'Progress Bar', $total );
            $i        = 0;
            while ( $i < $total ) {
                $progress->tick();
                sleep(1); // Remove this from your production code, only here to slow down the process so you can see it work.
                $i++;
            }
            $progress->finish();
            WP_CLI::line( 'Example Complete' );
        }
    }

    WP_CLI::add_command('foody', 'Foody_WhiteLabelCmdExport');

}