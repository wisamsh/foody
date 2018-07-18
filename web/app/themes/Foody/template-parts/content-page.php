<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Foody
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php

	the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' );
	?>

    <div class="entry-content">
		<?php
		the_content();

		edit_post_link( __( 'Edit', 'Foody' ), '<span class="edit-link">', '</span>' );
		?>
    </div><!-- .entry-content -->
</article><!-- #post-## -->
