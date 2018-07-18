<?php
/**
 * Template Name: Full Width Page
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */

get_header(); ?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content container" class="site-content" role="main">

			<?php if (has_post_thumbnail( $post->ID ) ): ?>
				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ),'full'); ?>
                <div class="cover-image">
                    <img src="<?php echo $image[0]; ?>" alt="">
                </div>
			<?php endif; ?>

			<?php if (function_exists('bootstrap_breadcrumb')): ?>

				<?php bootstrap_breadcrumb(); ?>

			<?php endif; ?>


			<?php
				the_content();
			?>
		</div><!-- #content -->

	</div><!-- #primary -->
</div><!-- #main-content -->

<?php
//get_sidebar();
get_footer();
