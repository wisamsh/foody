<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/14/18
 * Time: 1:27 PM
 */

the_content();
/** @var Foody_Article $foody_page */
$foody_page = Foody_PageContentFactory::get_instance()->get_page();;
?>


<section class="categories section">
    <h2 class="title">
        <?php echo __('קטגוריות') ?>
    </h2>
    <?php
    echo get_the_category_list('', '');
    ?>

</section>

<?php
$tags = wp_get_post_tags(get_the_ID());
if (!empty($tags)) {
    ?>
    <section class="tags section">
        <h2 class="title">
            <?php echo __('תגיות', 'foody') ?>
        </h2>

        <?php

        foody_get_template_part(get_template_directory() . '/template-parts/content-tags.php', $tags);
        ?>
    </section>
    <?php
}
?>

<section class="newsletter">
    <?php $foody_page->newsletter(); ?>

</section>

<section class="comments section">
    <?php

    $template = '';
    if (wp_is_mobile()) {
        $template = '/comments-mobile.php';
    }
    comments_template($template);
    ?>
</section>
