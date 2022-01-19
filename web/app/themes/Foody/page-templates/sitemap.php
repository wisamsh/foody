<?php
/**
 * Template Name: Wisam SiteMap
 * 
 */
get_header();
$foody_sitemap = new Foody_wsitemap();
$FoodyPosts = $foody_sitemap->get_posts_map('post');
$FoodyRecipes = $foody_sitemap->get_posts_map('foody_recipe');
//$FoodyArticles = $foody_sitemap->get_posts_map('page');
$foody_accessory = $foody_sitemap->get_posts_map('foody_accessory');
$foody_technique = $foody_sitemap->get_posts_map('foody_technique');
$foody_ingredient = $foody_sitemap->get_posts_map('foody_ingredient');
?>
<br>
<h1>מפת האתר</h1>
<h2>מתכונים</h2>
<?php echo $FoodyRecipes;?>


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





<?php
get_footer();
?>

<?php if (wp_is_mobile()) {
    require(get_template_directory() . '/components/mobile_bottom_menu.php');
}
?>