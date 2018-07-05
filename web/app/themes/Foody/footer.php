<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Foody
 */
$footer = new Foody_Footer();
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">

        <?php

        $footer->menu();

        ?>

	</footer><!-- #colophon -->
</div><!-- #page -->



<?php wp_footer(); ?>

</body>
</html>
