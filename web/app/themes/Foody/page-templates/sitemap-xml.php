<?php
/**
 * Template Name: XML Wisam SiteMap
 * 
 */
?>
<?php 
header('Content-Type: application/xml; charset=utf-8');
//get_header();
?>
<?php
echo'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$foody_sitemap = new Foody_wsitemap();
$FoodyPosts = $foody_sitemap->get_posts_map('post', true);
$FoodyRecipes = $foody_sitemap->get_posts_map('foody_recipe', true);
$questions = $foody_sitemap->get_posts_map('questions');
$foody_accessory = $foody_sitemap->get_posts_map('foody_accessory', true);
$foody_technique = $foody_sitemap->get_posts_map('foody_technique', true);
$foody_ingredient = $foody_sitemap->get_posts_map('foody_ingredient', true);
$foody_feed_channel = $foody_sitemap->get_posts_map('foody_feed_channel', true);
$foody_sitemap->MobileattrMap();


//$foody_filter = $foody_sitemap->get_posts_map('foody_filter');

echo $FoodyRecipes;
echo $FoodyPosts;
echo '</urlset>';
?>
