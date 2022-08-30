<?php

/**
* Created by PhpStorm.
* User: moveosoftware
* Date: 10/14/18
* Time: 1:27 PM
*/


/** @var Foody_Article $foody_page */
$foody_page = Foody_PageContentFactory::get_instance()->get_page();
echo $foody_page->Article_Style_Emplementor();
?>


<?php if ($foody_page->Get_Recipes_Title() != "") { ?>
<section class="categories section no-print">



<?php echo $foody_page->Get_Content_Before_Items();
echo $foody_page->Get_Area_Photo_Link();
?>

<div class="content_areas"></div>

<h1 id="area_0" class="recipe_title" style="font-size: 1.8em;">
<b><?php echo ($foody_page->Get_Recipes_Title()); ?></b>
</h2>


</section>

<?php } ?>

<?php echo $foody_page->Go_Recipes_For_Posts(); ?>

<?php
echo $foody_page->Get_Schedual_Photos();
?>

<?php echo $foody_page->tiktok_video();?>
<?php echo $foody_page->YouTubeShort();?>

<?php
the_content();
?>


<section class="categories section no-print">
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
<section class="tags section no-print">
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

<section class="newsletter no-print">
<?php $foody_page->newsletter(); ?>

</section>

<section class="comments section no-print">
<?php
if (is_comments_open($foody_page->id)) {
$template = '';
if (wp_is_mobile()) {
$template = '/comments-mobile.php';
}
comments_template($template);
}
?>
</section>
<?php if (function_exists('footabc_add_code_to_content')) : ?>
<section class="footab-container">
<?php echo footabc_add_code_to_content(); ?>
</section>
<?php endif; ?>

<script>

function ScrollTo(element){

jQuery('html, body').animate({
scrollTop: jQuery("#" + element).offset().top - 5
}, 500 );


}

jQuery( document ).ready(function() {


jQuery( "input[id^='area_photo_']" ).each(function( index ) {
//console.log( index + ": " + jQuery( this ).val() );
let html = '<div onclick="ScrollTo(`area_' + index + '`)" class="img_holder"><img class="img_ankor" src="'+jQuery( this ).val()+'" /></div>';
 
jQuery(".content_areas").append(html);
});
//jQuery(".content_areas").append(jQuery( this ).val());

});
</script>