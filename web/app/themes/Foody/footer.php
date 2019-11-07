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

<?php if (!empty($edit_link = get_edit_post_link())): ?>
    <div dir="rtl" style="text-align: right; max-width: 960px;margin: 0 auto;">
        <a href="<?php echo $edit_link ?>">
            <?php echo __('ערוך') ?>
        </a>
    </div>
<?php endif; ?>
<footer id="colophon" class="site-footer no-print">

    <section class="footer-social-container d-block d-lg-none">
        <?php dynamic_sidebar('foody-social'); ?>
    </section>

    <section class="newsletter d-block d-lg-none">
        <?php
        $footer->newsletter(get_option('foody_id_for_newsletter'));
        ?>

    </section>

    <section class="social d-block d-lg-none">
        <?php
        $show_instagram = get_theme_mod('foody_show_social_instagram');
        $instagram_link = get_theme_mod('foody_social_instagram');
        $show_facebook = get_theme_mod('foody_show_social_facebook');
        $facebook_link = get_theme_mod('foody_social_facebook');
        $show_youtube = get_theme_mod('foody_show_social_youtube');
        $youtube_link = get_theme_mod('foody_social_youtube');
        foody_get_template_part(get_template_directory() . '/template-parts/footer-social-bar.php',
            [
                'show_instagram' => $show_instagram,
                'show_facebook' => $show_facebook,
                'show_youtube' => $show_youtube,
                'instagram_link' => $instagram_link,
                'facebook_link' => $facebook_link,
                'youtube_link' => $youtube_link
            ]
        );
        ?>
    </section>

    <?php

    $footer->menu();

    ?>

    <section class="footer-pages footer-pages-mobile d-block d-lg-none">
        <ul>
            <?php $footer->display_menu_items($footer->footer_pages) ?>
        </ul>
        <section class="foody-israel-footer">
            <?php $footer->the_foody_israel(true); ?>
        </section>
        <?php if (get_theme_mod('foody_show_moveo_logo', true)) : ?>
            <section class="powered-by">
                <?php $footer->the_moveo() ?>
            </section>
        <?php endif; ?>


    </section>
    <?php

    if (get_theme_mod('show_in_all_the_site') ||
        ((is_front_page() || is_page(get_page_by_title(__('המרת מידות ומשקלות')))) && get_theme_mod('show_in_main_pages')) ||
        (is_single() && get_theme_mod('show_in_post_pages')) ||
        ((!is_front_page() && !is_page(get_page_by_title(__('המרת מידות ומשקלות'))) && !is_single()) && get_theme_mod('show_in_all_other_pages'))) {


        $link_page = get_page_by_title(__('Gift card'));
        $is_iframe = get_theme_mod('is_iframe');
        if ($is_iframe) {
            $button_link = get_permalink($link_page->ID);
        } else {
            $button_link = $link_page->post_content;
        }
        if (get_theme_mod('show_image_without_text')) {

            $banner_args = [
                'dest_id' => $link_page->ID,
                'page_id' => 'popup-banner',
                'desktop_img' => get_theme_mod('image_without_text_desktop'),
                'mobile_img' => get_theme_mod('image_without_text_mobile'),
                'banner_link' => $button_link,
                'button_text' => get_theme_mod('text_for_button'),
                'is_iframe' => $is_iframe
            ];
            foody_get_template_part(get_template_directory() . '/template-parts/common/popup-banner.php', $banner_args);
        } elseif (get_theme_mod('show_image_with_text')) {
            $banner_args = [
                'dest_id' => $link_page->ID,
                'page_id' => 'popup-banner',
                'desktop_img' => get_theme_mod('image_with_text_desktop'),
                'mobile_img' => get_theme_mod('image_with_text_mobile'),
                'banner_text' => get_theme_mod('text_for_image'),
                'banner_link' => $button_link,
                'button_text' => get_theme_mod('text_for_button'),
                'is_iframe' => $is_iframe
            ];
            foody_get_template_part(get_template_directory() . '/template-parts/common/popup-banner.php', $banner_args);
        }
    }
    ?>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php
if (!is_user_logged_in() && (!function_exists('foody_is_registration_open') || foody_is_registration_open())) {
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

<?php
if (get_option('foody_show_newsletter_popup')) {
    $login_popup_args = [
        'id' => 'newsletter-modal',
        'body' => do_shortcode('[foody-newsletter]'),
        'btn_approve_classes' => 'hide',
        'btn_cancel_classes' => 'hide',
        'title' => '',
        'hide_buttons' => true,
        'close_id' => 'close-newsletter-popup'
    ];

    foody_get_template_part(get_template_directory() . '/template-parts/common/modal.php', $login_popup_args);
}
?>

<?php wp_footer(); ?>


<?php

$footer->add_nagish_li_script();

?>

</body>
</html>
