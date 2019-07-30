<?php /* Template Name: Foody Network Template */ ?>

<?php
get_header();
?>

<?php
// Start the loop.
while ( have_posts() ) : the_post();

	// Include the page content template.
	get_template_part( 'template-parts/content', 'page-foody_network' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		?>
        <div class="container no-padding comment-area-page">
			<?php comments_template(); ?>
        </div>
	<?php
	endif;

// End the loop.
endwhile;
?>

<?php get_footer();