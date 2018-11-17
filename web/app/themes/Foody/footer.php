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

    <section class="newsletter d-block d-lg-none">
        <?php
        $footer->newsletter();
        ?>

    </section>

    <section class="social d-block d-lg-none">
        <?php
        foody_get_template_part(get_template_directory() . '/template-parts/footer-social-bar.php');
        ?>
    </section>

    <?php

    $footer->menu();

    ?>

    <section class="footer-pages footer-pages-mobile d-block d-lg-none">
        <ul>
            <?php $footer->display_menu_items($footer->footer_pages) ?>
        </ul>
        <section class="powered-by">
            <?php $footer->the_moveo() ?>
        </section>


    </section>

</footer><!-- #colophon -->
</div><!-- #page -->


<?php wp_footer(); ?>

</body>
</html>
