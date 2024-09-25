<?php 
//Wisam Code Checking Redirection : 
$CheckRedirectionPage = new CheckRedirectionPage;
 print_r($CheckRedirectionPage->GetRedirectionPages());

require(get_template_directory().'/inc/classes/class-bootstrap.php');


$show_accessibility = get_theme_mod('foody_show_accessibility') || get_theme_mod('show_white_label_accessibility');
$google_site_verification_id = get_option( 'foody_google_site_verification_id', false );
// always show on main site
if (!is_multisite() || is_main_site()) {
$show_accessibility = true;
}
?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<?php // for facebook metatags


//require (__DIR__ . '/w_helpers/taboola_in_head.php');
?>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
<?php require( __DIR__ . '/cache-tags.php' );?>
<?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<?php } ?>
<?php if($google_site_verification_id){ ?>
<meta name="google-site-verification" content="<?php echo $google_site_verification_id; ?>"/>
<?php } ?>
<meta name="theme-color" content="#ffffff">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php 
//Wisam : this fo white labels only - meta description and meta keywords======================
if($_SERVER['HTTP_HOST'] != "foody.co.il"){
require (__DIR__ . '/w_helpers/whitelabels_category-seo.php');
}
//End meta for whitlabels=====================================================================
?>
<?php Foody_Header::google_tag_manager(); ?>
<?php if (get_current_blog_id() == 2) { ?>
<script data-ad-client="ca-pub-3607762765478350" async
src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script type="text/javascript">
window._taboola = window._taboola || [];
_taboola.push({article:'auto'});
!function (e, f, u, i) {
if (!document.getElementById(i)){
e.async = 1;
e.src = u;
                    e.id = i;
f.parentNode.insertBefore(e, f);
                }
}(document.createElement('script'),
document.getElementsByTagName('script')[0],
'//cdn.taboola.com/libtrc/karingoren/loader.js',
'tb_loader_script');
if(window.performance && typeof window.performance.mark == 'function')
{window.performance.mark('tbl_ic');}

</script>

<?php } ?>