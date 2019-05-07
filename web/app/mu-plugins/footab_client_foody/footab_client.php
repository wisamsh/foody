<?php
/*
Plugin Name: Foody Taboola Client
Plugin URI: http://www.foody.co.il
Description: A plugin to easily install the foody taboola options
Version: 1.0.5
Author: Uri Chachick
Author URI: http://www.glove.co.il
License: A "Slug" license name e.g. GPL2
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  footab_client
Domain Path:  /languages
*/

load_plugin_textdomain('footab_client', false, basename(dirname(__FILE__)) . '/languages');

define('FOOTABC_PATH', plugin_dir_path(__FILE__));
define('FOOTABC_URL', plugin_dir_url(__FILE__));


require 'plugin-update-checker/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4p4_Factory::buildUpdateChecker(
    'https://foody.co.il/footab_client/footabc_data.json',
    __FILE__,
    'footab_client'
);

/*
 * Enqueueuing styles
 */

function footabc_files_enqueue()
{
    wp_register_style('footabc_style', plugins_url('style.css', __FILE__));
    wp_enqueue_style('footabc_style'); // Enqueue it!
}

add_action('admin_enqueue_scripts', 'footabc_files_enqueue');

function footabc_frontend_enqueue()
{

    wp_register_style('footabc_front_style', plugins_url('front_style.css', __FILE__));
    wp_enqueue_style('footabc_front_style'); // Enqueue it!
}

add_action('wp_enqueue_scripts', 'footabc_frontend_enqueue');

/*
 * sizes
 */

add_action( 'init', 'footabc_image_sizes' );
function footabc_image_sizes() {
    add_image_size( 'footab_rss', 300, 300, true ); //mobile
}

/*
 * Rss page
 */

add_action('init', 'footabc_rss');
function footabc_rss(){
    $name       = 'footabc_rss';

    $registered = FALSE;
    add_feed($name, 'footabc_rss_func');

    $rules = get_option( 'rewrite_rules' );
    $feeds = array_keys( $rules, 'index.php?&feed=$matches[1]' );

    foreach ( $feeds as $feed )
    {
        if ( FALSE !== strpos( $feed, $name ) )
            $registered = TRUE;
    }

    // Feed not yet registered, so lets flush the rules once.
    if ( ! $registered )
        flush_rewrite_rules( FALSE );
}

function footabc_rss_func(){
    include(FOOTABC_PATH . 'footabc-rss.php');
    global $wp_rewrite;
    $wp_rewrite->flush_rules( false );
}


/*
 * Full rss page
 */

add_action('init', 'footabc_rss_full');
function footabc_rss_full(){
    $name       = 'footabc_rss_full';

    $registered = FALSE;
    add_feed($name, 'footabc_rss_full_func');

    $rules = get_option( 'rewrite_rules' );
    $feeds = array_keys( $rules, 'index.php?&feed=$matches[1]' );

    foreach ( $feeds as $feed )
    {
        if ( FALSE !== strpos( $feed, $name ) )
            $registered = TRUE;
    }

    // Feed not yet registered, so lets flush the rules once.
    if ( ! $registered )
        flush_rewrite_rules( FALSE );
}

function footabc_rss_full_func(){
    include(FOOTABC_PATH . 'footabc-rss-full.php');
    global $wp_rewrite;
    $wp_rewrite->flush_rules( false );
}

/*
 * option page
 */

include(FOOTABC_PATH . 'footabc_options.php');

$options = footabc_get_options();


function update_footabc_data()
{
    $options = footabc_get_options();

    $footabc_url = isset($options['footabc_url']) ? $options['footabc_url'] : '';

    /*
     * getting fresh copy of url once a day
     */
    $now = new DateTime();
    if (isset($_GET['reset']) && $_GET['reset']) {
        update_option('footabc_data', '');
    }

    $footabc_file_time = get_option('footabc_file_time') ? get_option('footabc_file_time') : new DateTime("-1 day");
    if ($footabc_url && get_post_type() != 'footab_sites' && (!get_option('footabc_data') || $now > $footabc_file_time)) {
        $footabc_remote_data = wp_remote_retrieve_body(wp_remote_get($footabc_url,array( 'timeout' => 120)));
        update_option("footabc_data", $footabc_remote_data);
        update_option('footabc_file_time', new DateTime("+1 day"));
    }
}
add_action('wp','update_footabc_data');


/*
 * showing the taboola module
 */
if (get_option('footabc_data')) {
    $footabc_data = get_option('footabc_data');
    $footabc_data = json_decode($footabc_data);

    function footabc_show_module()
    {
        /*
         * determines wether to show the module or not
         */
        $footab_show = false;
        $footabc_data = get_option('footabc_data');
        $footabc_data = json_decode($footabc_data);
        if (isset($footabc_data->_footab_show_module) && $footabc_data->_footab_show_module && !is_admin()) {
            $footab_show = true;
            if (isset($footabc_data->_footab_test_mode) && $footabc_data->_footab_test_mode) {
                $footab_show = false;
                if (isset($_GET['test']) && $_GET['test'] == '1') {
                    $footab_show = true;
                }
            } else {
                $footab_show = true;
            }
        }

        return $footab_show;
    }


    add_action('init', 'footabc_show_module');

    function footabc_add_code_to_content($content = '')
    {
        if ((is_single() || is_singular('foody_recipe') || is_singular('recipe')) && in_the_loop() && is_main_query()) {
            $footab_show = footabc_show_module();
            $footabc_data = get_option('footabc_data');
            $footabc_data = json_decode($footabc_data);

            if (isset($footabc_data->_footab_top_image) && $footabc_data->_footab_top_image) {
                $footab_top_image = $footabc_data->_footab_top_image;
            } else {
                $footab_top_image = FOOTABC_URL.'imgs/footab_top.png';
            }

            if (isset($footabc_data->_footab_border_color) && $footabc_data->_footab_border_color) {
                $footab_border_color = $footabc_data->_footab_border_color;
            } else {
                $footab_border_color = 'transparent';
            }

            $footab_border_location_up = '0';
            $footab_border_location_right = '0';
            $footab_border_location_down = '0';
            $footab_border_location_left = '0';

            if (isset($footabc_data->_footab_border_location) && $footabc_data->_footab_border_location) {
                $footabc_border_location = $footabc_data->_footab_border_location;
                if (in_array('up', $footabc_border_location)) {
                    $footab_border_location_up = '2px';
                } else {
                    $footab_border_location_up = '0';
                }

                if (in_array('right', $footabc_border_location)) {
                    $footab_border_location_right = '2px';
                } else {
                    $footab_border_location_right = '0';
                }

                if (in_array('down', $footabc_border_location)) {
                    $footab_border_location_down = '2px';
                } else {
                    $footab_border_location_down = '0';
                }

                if (in_array('left', $footabc_border_location)) {
                    $footab_border_location_left = '2px';
                } else {
                    $footab_border_location_left = '0';
                }
            }

            if (isset($footabc_data->_footab_taboola_code) && $footabc_data->_footab_taboola_code) {
                $footab_taboola_code = htmlspecialchars_decode($footabc_data->_footab_taboola_code);
            } else {
                $footab_taboola_code = '';
            }
            if (isset($footabc_data->_footab_top_text) && $footabc_data->_footab_top_text) {
                $footab_top_text = $footabc_data->_footab_top_text;
            } else {
                $footab_top_text = '';
            }


            if ($footab_show) {
                $footab_module = '';
                $footab_module .= '<style>';
                $footab_module .= '.footab_wrapper {';
                $footab_module .= 'font-family: Assistant, arial, sans-serif;';
                $footab_module .= 'font-size: 18px;';
                $footab_module .= 'border-top: ' . $footab_border_location_up . ' solid ' . $footab_border_color . ';';
                $footab_module .= 'border-right: ' . $footab_border_location_right . ' solid ' . $footab_border_color . ';';
                $footab_module .= 'border-bottom: ' . $footab_border_location_down . ' solid ' . $footab_border_color . ';';
                $footab_module .= 'border-left: ' . $footab_border_location_left . ' solid ' . $footab_border_color . ';';
                $footab_module .= '}';
                $footab_module .= '</style>';
                $footab_module .= '<div class="footab_wrapper">';
                $footab_module .= '<div class="footab_top">';
                $footab_module .= '<div class="top_text">' . $footab_top_text . '</div>';
                $footab_module .= '<div class="top_image"><a href="https://foody.co.il/"><img src="' . $footab_top_image . '" alt="powered by foody" /></a></div>';
                $footab_module .= '</div>';
                $footab_module .= '<div class="taboola_module">' . $footab_taboola_code . '</div>';
                $footab_module .= '</div>';


            } else {
                $footab_module = '';
            }
            return $content . $footab_module;
        } else {
            return $content;
        }
    }

    add_filter('the_content', 'footabc_add_code_to_content');

    function footabc_taboola_head()
    {
        $footab_show = footabc_show_module();
        if ($footab_show) {
            $footabc_data =  get_option('footabc_data');
            $footabc_data =  json_decode($footabc_data);
            echo  htmlspecialchars_decode($footabc_data->_footab_taboola_code_head);
            if ($footabc_data->_footab_analytics_code) {
                echo  htmlspecialchars_decode($footabc_data->_footab_analytics_code);
            }

        }
    }

    add_action('wp_head', 'footabc_taboola_head');

    function footabc_taboola_footer()
    {
        $footab_show = footabc_show_module();
        if ($footab_show) {
            $footabc_data =  get_option('footabc_data');
            $footabc_data =  json_decode($footabc_data);
            echo  htmlspecialchars_decode($footabc_data->_footab_taboola_code_footer);
        }
    }

    add_action('wp_footer', 'footabc_taboola_footer');
}



