<?php
/**
 * Template Name: Foody Campaign Extended
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$e_book_extended = new Foody_Campaign_Extended();
$e_book          = $e_book_extended->campaign;

?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
			<?php foody_get_template_part( get_template_directory() . '/template-parts/content-campaign-page.php', [ 'ebook' => $e_book ] ); ?>

			<?php if ( $e_book_extended->show_how_i_did ): ?>
                <div class="campaign-how-i-did">
					<?php $e_book_extended->how_i_did() ?>
                </div>
			<?php endif; ?>
        </div><!-- #primary -->
    </div><!-- #main-content -->
<?php

get_footer();
