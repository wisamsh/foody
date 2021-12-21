<?php

$related_side =  get_field('fq_automatic_recepies_sidebar', get_the_ID());
$post_thumbs_side = array();
if ($related_side == 'ידני') {
    $related_side = get_field('fq_handy_recepie_repeater_sidebar', get_the_ID());

    foreach ($related_side as $related_side) {
        $post_thumbs_side['thumb'][] =  get_the_post_thumbnail_url($related_side['fq_recepie_handy_pick']);
        $post_thumbs_side['title'][] =  get_the_title($related_side['fq_recepie_handy_pick']);
        $post_thumbs_side['url'][] = get_permalink($related_side['fq_recepie_handy_pick']);
    }
}
if ($related_side != 'ידני') {

    $args_side = array(
        'numberposts'      => 4,
        'category'         => $category,
        'orderby'          => 'rand',
        'post_type'        => 'foody_recipe',
        'suppress_filters' => false,

    );
    $related_side = get_posts($args_side);
    foreach ($related_side as $k => $related_side) {
        $post_ID_side = ($related_side->ID);
        $post_thumbs_side[$k]['thumb'] =  get_the_post_thumbnail_url($post_ID_side);
        $post_thumbs_side[$k]['ID'] = $post_ID_side;
        $post_thumbs_side[$k]['title'] =  get_the_title($post_ID_side);
        $post_thumbs_side[$k]['url'] = get_permalink($post_ID_side);
        $post_thumbs_side[$k]['post_author'] = $related_side->post_author;
        $at = str_replace('@', '', get_the_author_meta('user_email', $related_side->post_author));
        $post_thumbs_side[$k]['user_email'] =  str_replace('.', '-', $at);
    }
}
?>
<h2 class="title">מתכונים נוספים</h2>

<div class="row">
    <?php foreach ($post_thumbs_side as $post_thumbs_side) { ?>
        <div class="col-12 siderelated">
            <a href="<?php echo $post_thumbs_side['url'] ?>">
                <img src="<?php echo $post_thumbs_side['thumb'] ?>" />
                <div class="details">
                    <div class="post-title"><?php echo $post_thumbs_side['title'] ?></div>
                </div>
            </a>
            <a href="/author/<?php echo $post_thumbs_side['user_email']; ?>" class="author-name">
                <b><?php echo (get_the_author_meta('display_name', $post_thumbs_side['post_author'])); ?></b>
            </a>
            <div class="excerpt"><?php echo get_field('mobile_caption', $post_thumbs_side['ID']); ?></div>
        </div>

    <?php } ?>


</div>