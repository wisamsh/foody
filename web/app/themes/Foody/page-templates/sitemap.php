<?php
/**
 * Template Name: Wisam SiteMap
 * 
 */
get_header();

function FAQ_Scripts()
{
    $VersionHashCss = date('Y.m.d h.m');
    wp_register_style('QuestionsCSS', get_template_directory_uri() . '/components/css/css_questions.css', array(), $VersionHashCss);
    wp_enqueue_style('QuestionsCSS');
}
add_action('get_footer', 'FAQ_Scripts');
$foody_sitemap = new Foody_wsitemap();
$FoodyPosts = $foody_sitemap->get_posts_map('post');
$FoodyRecipes = $foody_sitemap->get_posts_map('foody_recipe');
$questions = $foody_sitemap->get_posts_map('questions');
$foody_accessory = $foody_sitemap->get_posts_map('foody_accessory');
$foody_technique = $foody_sitemap->get_posts_map('foody_technique');
$foody_ingredient = $foody_sitemap->get_posts_map('foody_ingredient');
$foody_feed_channel = $foody_sitemap->get_posts_map('foody_feed_channel');
$foody_sitemap->MobileattrMap();


//$foody_filter = $foody_sitemap->get_posts_map('foody_filter');

?>

<div class="row m-0">
    
<section class="cover-image no-print">
        <div class="cover-image">
            <?php //echo $Foody_Questions->doCommercialBanner(); ?>
        </div>
    </section>
    

    <article class="content">
        <section class="details-container">
        <br>
        <?php $foody_sitemap->Do_FoodyBeadcrumbs(); ?>
  
<br>
<h1 class="uniq title" >מפת האתר</h1>



<h1 class="title" >מתכונים</h2>
<?php echo $FoodyRecipes;?>

<h1 class="title">מתחמי פידים</h2>
<?php 
echo($foody_feed_channel );
?>


<h1 class="title">שאלות תשובות</h2>
<?php echo $questions;?>

<h1 class="title">אביזרים</h2>
<?php 
echo($foody_accessory );
?>

<h1 class="title">טכניקות</h2>
<?php 
echo($foody_technique );
?>

<h1 class="title">מצרכים</h2>
<?php 
echo($foody_ingredient );
?>


<h1 class="title">פוסטים</h2>
<?php 
echo($FoodyPosts );
?>

<h1 class="title">תגיות</h2>
<?php 
echo($foody_sitemap->get_the_tags() );
?>

<h1 class="title">קטגוריות</h2>
<?php 
echo($foody_sitemap->get_the_sitemap_categories());
?>



        </section>
    </article></div>


<?php
get_footer();
?>

<?php if (wp_is_mobile()) {
    require(get_template_directory() . '/components/mobile_bottom_menu.php');
}
?>