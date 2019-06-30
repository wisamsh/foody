<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/25/19
 * Time: 7:15 PM
 */

/** @var Foody_Feed_Filter $feed_filter */
/** @noinspection PhpUndefinedVariableInspection */
$feed_filter = $template_args['page'];


$feed_filter->feed();

$show_google_adx   = get_option( 'foody_show_google_adx' );
$google_adx_script = get_option( 'foody_google_adx_script' );

if ( $show_google_adx && ! empty( $google_adx_script ) ) {
	echo '<section class="google-adx-container col-lg-9 col-12">' . $google_adx_script . '</section>';
}