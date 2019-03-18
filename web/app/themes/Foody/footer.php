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

<?php if ( !empty($edit_link = get_edit_post_link())): ?>
<div dir="rtl" style="text-align: right; max-width: 960px;margin: 0 auto;">
    <a href="<?php echo $edit_link?>">
        <?php echo __('ערוך')?>
    </a>
</div>
<?php endif; ?>
<footer id="colophon" class="site-footer no-print">



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

<?php
if (!is_user_logged_in()) {
    $login_popup_args = [
        'id' => 'login-modal',
        'body' => do_shortcode('[foody-login]'),
        'btn_approve_classes' => 'hide',
        'btn_cancel_classes' => 'hide',
        'title' => '',
        'hide_buttons' => true
    ];

    foody_get_template_part(get_template_directory() . '/template-parts/common/modal.php', $login_popup_args);
}

?>

<?php wp_footer(); ?>

</body>
</html>
