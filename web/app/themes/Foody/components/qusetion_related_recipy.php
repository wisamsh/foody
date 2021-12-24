<?php 
$related =  get_field('fq_automatic_recepies', get_the_ID());
$post_thumbs=array();
if($related == 'ידני'){
    $recipies = get_field('fq_handy_recepie_repeater', get_the_ID());

foreach($recipies as $k => $recipies){
   $post_thumbs[$k]['thumb'] =  get_the_post_thumbnail_url($recipies['fq_recepie_handy_pick']) ;
   $post_thumbs[$k]['title'] =  get_the_title($recipies['fq_recepie_handy_pick']) ;
   $post_thumbs[$k]['url'] = get_permalink($recipies['fq_recepie_handy_pick']);

}
}
if($related != 'ידני'){
    
$args = array(
    'numberposts'      => 4,
    'category'         => $category,
    'orderby'          => 'rand',
    'post_type'        => 'foody_recipe',
    'suppress_filters' => false,

);
$related = get_posts( $args );
foreach($related as $k => $related){
    $post_ID = ($related->ID );
    $post_thumbs[$k]['thumb'] =  get_the_post_thumbnail_url($post_ID) ;
    $post_thumbs[$k]['title'] =  get_the_title($post_ID) ;
    $post_thumbs[$k]['url'] = get_permalink($post_ID);
}

}

?>

<h2 class="title">מתכונים נוספים שכדאי לכם לנסות</h2>
<div class="container fluid">
    <div class="row">
    <?php foreach($post_thumbs as $post_thumbs){?>   
    <div class="related_recepies_conduct">
            <a href="<?php echo $post_thumbs['url'];?>"><img src="<?php echo $post_thumbs['thumb'];?>"/>
            <p><?php echo $post_thumbs['title'];?></p></a>
        </div>
       <?php }?>
    </div>
</div>

