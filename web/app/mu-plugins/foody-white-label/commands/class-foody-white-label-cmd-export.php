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
            $post_thumbnail_id = get_post_thumbnail_id(65);
            if (!empty($post_thumbnail_id)) {
                $image_url = wp_get_attachment_image_src($post_thumbnail_id, 'full');
                if (!empty($image_url)) {
                    $image_url = $image_url[0];
                    WP_CLI::success($image_url);
                }else{
                    WP_CLI::error('no image');
                }
            }
        }
    }

    WP_CLI::add_command('foody', 'Foody_WhiteLabelCmdExport');

}