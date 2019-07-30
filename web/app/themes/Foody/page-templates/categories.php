<?php
/**
 * Template Name: Categories
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
get_header();

$categories = new Foody_Categories();

?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="content container" class="site-content" role="main">
				<?php if ( function_exists( 'bootstrap_breadcrumb' ) ): ?>

					<?php bootstrap_breadcrumb(); ?>

				<?php endif; ?>

				<?php echo the_title( '<h1 class="title mt-0 mb-0">', '</h1>' ) ?>

				<?php $categories->display(); ?>

				<?php Foody_Seo::seo() ?>
            </div><!-- #content -->
        </div><!-- #primary -->
    </div><!-- #main-content -->


<?php
get_footer();