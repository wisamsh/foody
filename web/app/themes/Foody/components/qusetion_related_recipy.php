<?php 
$related =  get_field('fq_automatic_recepies', get_the_ID());
$post_thumbs=array();
if($related == 'ידני'){
    $recipies = get_field('fq_handy_recepie_repeater', get_the_ID());

foreach($recipies as $recipies){
   $post_thumbs['thumb'][] =  get_the_post_thumbnail_url($recipies['fq_recepie_handy_pick']) ;
   $post_thumbs['title'][] =  get_the_title($recipies['fq_recepie_handy_pick']) ;
   $post_thumbs['url'][] = get_permalink($recipies['fq_recepie_handy_pick']);

}
}
if($related != 'ידני'){
    
$args = array(
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
$related = get_posts( $args );
foreach($related as $related){
    $post_ID = ($related->ID );
    $post_thumbs['thumb'][] =  get_the_post_thumbnail_url($post_ID) ;
    $post_thumbs['title'][] =  get_the_title($post_ID) ;
    $post_thumbs['url'][] = get_permalink($post_ID);
}

}
?>
<h2 class="title">מתכונים נוספים שכדי לכם לנסות</h2>
<div class="container fluid">
    <div class="row">
        <div class="related_recepies_conduct">
            <a href="<?php echo $post_thumbs['url'][0]?>"><img src="<?php echo $post_thumbs['thumb'][0]?>"/>
            <p><?php echo $post_thumbs['title'][0]?></p></a>
        </div>
        <div class="related_recepies_conduct">
        <a href="<?php echo $post_thumbs['url'][1]?>"><img src="<?php echo $post_thumbs['thumb'][1]?>"/>
            <p><?php echo $post_thumbs['title'][1]?></p></a>
        </div>
        <div class="related_recepies_conduct">
        <a href="<?php echo $post_thumbs['url'][2]?>"><img src="<?php echo $post_thumbs['thumb'][2]?>"/>
            <p><?php echo $post_thumbs['title'][2]?></p></a>
        </div>
        <div class="related_recepies_conduct">
        <a href="<?php echo $post_thumbs['url'][3]?>"><img src="<?php echo $post_thumbs['thumb'][3]?>"/>
            <p><?php echo $post_thumbs['title'][3]?></p></a>
        </div>
    </div>
</div>

