<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 1:16 PM
 */

$buttons = ['mail', 'pinterest', 'whatsapp'];

if (!wp_is_mobile()) {
    array_unshift($buttons, 'print');
}


$buttons_attr = implode(',', $buttons);

$social_icons = do_shortcode('[easy-social-share buttons="' . $buttons_attr . '" template="11" counters=0 style="icon" point_type="simple"]');

?>


<div class=" social col">
    <?php echo $social_icons ?>
    <button class="btn btn-facebook btn-facebook-share">
        <a target="popup"
           onclick="window.open(this.href,'popup','width=600,height=600'); return false;"
           href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()) ?>">
            <?php echo __('שתף', 'foody') ?>
        </a>
    </button>

    <section class="d-none d-lg-inline-flex">
        <?php Foody_Recipe::ratings() ?>
    </section>
</div>

