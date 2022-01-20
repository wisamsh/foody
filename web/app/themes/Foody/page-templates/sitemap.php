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
<br>
<h1 class="uniq">מפת האתר</h1>



<h2>מתכונים</h2>
<?php echo $FoodyRecipes;?>

<h2>מתחמי פידים</h2>
<?php 
echo($foody_feed_channel );
?>


<h2>שאלות תשובות</h2>
<?php echo $questions;?>

<h2>אביזרים</h2>
<?php 
echo($foody_accessory );
?>

<h2>טכניקות</h2>
<?php 
echo($foody_technique );
?>

<h2>מצרכים</h2>
<?php 
echo($foody_ingredient );
?>


<h2>פוסטים</h2>
<?php 
echo($FoodyPosts );
?>

<h2>תגיות</h2>
<?php 
echo($foody_sitemap->get_the_tags() );
?>

<h2>קטגוריות</h2>
<?php 
echo($foody_sitemap->get_the_sitemap_categories());
?>






<?php
get_footer();
?>

<?php if (wp_is_mobile()) {
    require(get_template_directory() . '/components/mobile_bottom_menu.php');
}
?>