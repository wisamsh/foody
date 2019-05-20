<?php
/**
 * Template Name: Foody Campaign
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$e_book = isset( $template_args ) && isset( $template_args['ebook'] ) ? $template_args['ebook'] : '';
if ( empty( $e_book ) ) {
	$e_book = new Foody_Campaign();
}

?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <?php foody_get_template_part( get_template_directory() . '/template-parts/content-campaign-page.php', [ 'ebook' => $e_book ] ); ?>
        </div><!-- #primary -->
    </div><!-- #main-content -->

<?php

get_footer();
