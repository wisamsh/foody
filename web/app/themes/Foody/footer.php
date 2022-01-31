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
$post = get_post();
$feed_area_id = 0;
$use_general_banner = true;
$page = get_queried_object();

if (isset($_GET) && !empty($_GET) && isset($_GET['referer']) && $_GET['referer'] != 0) {
        if($post == null){
            $feed_area_id = $_GET['referer'];
        }
        elseif (!get_field('activate_banner', $post->ID)) {
            $feed_area_id = $_GET['referer'];
        }
        else {
            $feed_area_id = $post->ID;
        }
}
elseif (isset($post) && ($post->post_type == 'foody_recipe' || $post->post_type == 'post') && get_field('recipe_channel', $post->ID)){
    if (!get_field('activate_banner', $post->ID)) {
        $feed_area_id = get_field('recipe_channel', $post->ID);
    }
    else {
        $feed_area_id = $post->ID;
    }
}
elseif(isset($page->taxonomy) && $page->taxonomy === 'category' && get_field('recipe_channel', $page)){
    $feed_area_id = get_field('recipe_channel', $page);
}
elseif (isset($post) && ($post->post_type == 'foody_feed_channel' || $post->post_type == 'foody_recipe' || $post->post_type == 'post')) {
    $feed_area_id = $post->ID;
}

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
        <?php dynamic_sidebar('foody-social-mobile'); ?>
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
        <?php if((get_current_blog_id() == 1) || (get_current_blog_id() != 1 && !get_option( 'foody_remove_foodys_link_footer', false ))){ ?>
        <section class="foody-israel-footer">
            <?php $footer->the_foody_israel(true); ?>
        </section>
        <?php } ?>
        <?php if (get_theme_mod('foody_show_moveo_logo', true)) : ?>
            <section class="powered-by">
                <?php $footer->the_moveo() ?>
            </section>
        <?php endif; ?>


    </section>
    <?php
    if ($feed_area_id != 0) {
        if (get_field('activate_banner', $feed_area_id)) {
            $use_general_banner = false;
            $banners_list = get_field('banners_list', $feed_area_id);
            $banner = $footer->get_relevant_banner($banners_list[0]['banner']);
            if (!empty($banner)) {
                $link_page = get_page_by_title(__('Gift card'));
                if ($banner['is_iframe']) {
                    $button_link = get_permalink($link_page->ID) . '?alterLink=' . urlencode($banner['link']['url']);
                } else {
                    $button_link = $banner['link']['url'];
                }

                if ($banner['enable_banner_without_text']) {
                    $banner_args = [
                        'dest_id' => $link_page->ID,
                        'page_id' => 'popup-banner',
                        'desktop_img' => $banner['image_without_text']['url'],
                        'mobile_img' => $banner['image_without_text_mobile']['url'],
                        'banner_link' => $button_link,
                        'button_text' => $banner['text_for_button'],
                        'is_iframe' => $banner['is_iframe'],
                        'name' => $banner['name'],
                        'publisher' => $banner['publisher']
                    ];
                    foody_get_template_part(get_template_directory() . '/template-parts/common/popup-banner.php', $banner_args);
                } else {
                    $banner_args = [
                        'dest_id' => $link_page->ID,
                        'page_id' => 'popup-banner',
                        'desktop_img' => $banner['image_with_text']['url'],
                        'mobile_img' => $banner['image_with_text_mobile']['url'],
                        'banner_text' => $banner['text_for_banner'],
                        'banner_link' => $button_link,
                        'button_text' => $banner['text_for_button'],
                        'is_iframe' => $banner['is_iframe'],
                        'name' => $banner['name'],
                        'publisher' => $banner['publisher']
                    ];
                    foody_get_template_part(get_template_directory() . '/template-parts/common/popup-banner.php', $banner_args);
                }
            }
        }
    }

    if ($use_general_banner && (get_theme_mod('show_in_all_the_site') ||
            ((is_front_page() || is_page(get_page_by_title(__('המרת מידות ומשקלות')))) && get_theme_mod('show_in_main_pages')) ||
            (is_single() && get_theme_mod('show_in_post_pages')) ||
            ((!is_front_page() && !is_page(get_page_by_title(__('המרת מידות ומשקלות'))) && !is_single()) && get_theme_mod('show_in_all_other_pages')))) {


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
                'is_iframe' => $is_iframe,
                'name' => get_theme_mod('name_for_banner'),
                'publisher' => get_theme_mod('banner_publisher')
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
                'is_iframe' => $is_iframe,
                'name' => get_theme_mod('name_for_banner'),
                'publisher' => get_theme_mod('banner_publisher')
            ];
            foody_get_template_part(get_template_directory() . '/template-parts/common/popup-banner.php', $banner_args);
        }
    }
    ?>
</footer><!-- #colophon -->
<?php require (__DIR__ . '/w_helpers/taboola_in_footer.php');?>
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
//if (get_option('foody_show_newsletter_popup')) {
//    $newsletter_popup_args = [
//        'id' => 'newsletter-modal',
//        'body' => do_shortcode('[foody-newsletter]'),
//        'btn_approve_classes' => 'hide',
//        'btn_cancel_classes' => 'hide',
//        'title' => '',
//        'hide_buttons' => true,
//        'close_id' => 'close-newsletter-popup'
//    ];
//
//    foody_get_template_part(get_template_directory() . '/template-parts/common/modal.php', $newsletter_popup_args);
//}


?>
<?php wp_footer(); ?>


<?php

$footer->add_nagish_li_script();

?>




</body>
<?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php } ?>
<?php if(strpos(get_page_template(), 'foody-course-register.php')) { ?>
    <style>
        /* Media query for mobile viewport */
        @media screen and (max-width: 400px) { #bitcom-button-container { width: 100%; } }
        /* Media query for desktop viewport */
        @media screen and (min-width: 400px) { #bitcom-button-container { width: 250px; } }
    </style>
<?php } ?>
</html>