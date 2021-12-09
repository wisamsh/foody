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
        //'order'            => 'DESC',
        //'include'          => array(),
        // 'exclude'          => array(),
        // 'meta_key'         => '',
        // 'meta_value'       => '',
        'post_type'        => 'foody_recipe',
        'suppress_filters' => false,

    );
    $related_side = get_posts($args_side);
    foreach ($related_side as $related_side) {
        $post_ID_side = ($related_side->ID);
        $post_thumbs_side['thumb'][] =  get_the_post_thumbnail_url($post_ID_side);
        $post_thumbs_side['ID'][]= $post_ID_side;
        $post_thumbs_side['title'][] =  get_the_title($post_ID_side);
        $post_thumbs_side['url'][] = get_permalink($post_ID_side);
        $post_thumbs_side['post_author'][] = $related_side->post_author;
        $at = str_replace('@', '', get_the_author_meta('user_email', $related_side->post_author));
        $post_thumbs_side['user_email'][] =  str_replace('.', '-', $at);
    }
}
?>
<h2 class="title">מתכונים נוספים</h2>

<div class="row">

    <div class="col-12 siderelated">
        <a href="<?php echo $post_thumbs_side['url'][0] ?>">
        <img src="<?php echo $post_thumbs_side['thumb'][0] ?>" />
            <div class="details">
                <div class="post-title"><?php echo $post_thumbs_side['title'][0] ?></div>
            </div>
        </a>
        <a href="/author/<?php echo $post_thumbs_side['user_email'][0]; ?>" class="author-name">
            <b><?php echo (get_the_author_meta('display_name', $post_thumbs_side['post_author'][0])); ?></b>
        </a>
        <div class="excerpt"><?php echo get_field('mobile_caption', $post_thumbs_side['ID'][0]);?></div> 
    </div>

    <div class="col-12 siderelated">
        <a href="<?php echo $post_thumbs_side['url'][1] ?>">
        <img src="<?php echo $post_thumbs_side['thumb'][1] ?>" />
            <div class="details">
                <div class="post-title"><?php echo $post_thumbs_side['title'][1] ?></div>
            </div>
        </a>
        <a href="/author/<?php echo $post_thumbs_side['user_email'][1]; ?>" class="author-name">
            <b><?php echo (get_the_author_meta('display_name', $post_thumbs_side['post_author'][1])); ?></b>
        </a>
        <div class="excerpt"><?php echo get_field('mobile_caption', $post_thumbs_side['ID'][1]);?></div> 
    </div>


    <div class="col-12 siderelated">
        <a href="<?php echo $post_thumbs_side['url'][2] ?>">
        <img src="<?php echo $post_thumbs_side['thumb'][2] ?>" />
            <div class="details">
                <div class="post-title"><?php echo $post_thumbs_side['title'][2] ?></div>
            </div>
        </a>
        <a href="/author/<?php echo $post_thumbs_side['user_email'][2]; ?>" class="author-name">
            <b><?php echo (get_the_author_meta('display_name', $post_thumbs_side['post_author'][2])); ?></b>
        </a>
        <div class="excerpt"><?php echo get_field('mobile_caption', $post_thumbs_side['ID'][2]);?></div> 
    </div>


    <div class="col-12 siderelated">
        <a href="<?php echo $post_thumbs_side['url'][3] ?>">
        <img src="<?php echo $post_thumbs_side['thumb'][3] ?>" />
            <div class="details">
                <div class="post-title"><?php echo $post_thumbs_side['title'][3] ?></div>
            </div>
        </a>
        <a href="/author/<?php echo $post_thumbs_side['user_email'][3]; ?>" class="author-name">
            <b><?php echo (get_the_author_meta('display_name', $post_thumbs_side['post_author'][3])); ?></b>
        </a>
        <div class="excerpt"><?php echo get_field('mobile_caption', $post_thumbs_side['ID'][3]);?></div> 
    </div>


   
    
</div>