<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Foody
 */

get_header();
?>


    <div id="primary" class="content-area">
        <main id="main" class="site-main">

			<?php
			while ( have_posts() ) :
				the_post();

				foody_set_post_views( get_the_ID() );

				get_template_part( 'template-parts/single', get_post_type() );

			endwhile; // End of the loop.


			?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
//get_sidebar();
get_footer();
