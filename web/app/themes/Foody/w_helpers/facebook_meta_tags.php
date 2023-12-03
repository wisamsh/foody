<?php // need to change app_id to 242005383411645 on production?>
<?php $domain = array(
"staging.foody.co.il" => "300250737432710",
"foody.co.il" => "242005383411645",
"foody-local.co.il" => "123456"
);
$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$OG_image_url = get_field('facebook_share_image', 'option');


$get_the_post_thumbnail_url =get_the_post_thumbnail_url() ;
if($get_the_post_thumbnail_url == "" || empty($get_the_post_thumbnail_url) || (is_front_page() && $OG_image_url !="")){
$get_the_post_thumbnail_url = $OG_image_url;
}

?>

<meta property="fb:app_id" content="<?php echo $domain[$_SERVER['HTTP_HOST']];?>" />
<meta property="og:url" content="<?php echo $actual_link ;?>" />
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php echo get_the_title();?>" />
<meta property="og:description" content="פודי - המתכונים הכי טעימים של השפים והבלוגרים המובילים והכתבות הכי מעניינות. FOODY אתר האוכל הגדול בישראל - היכנסו לאתר לפרטים נוספים ובתאבון!" />
<meta property="og:image" itemprop="image" content="<?php echo $get_the_post_thumbnail_url;?>" />
<meta name="facebook-domain-verification" content="2g8c6oi7iii72tdemqqyr55bp9yey9" />
<meta property="article:content_tier" content="free"/>



<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:url" content="<?php echo $actual_link ;?>"/>
<meta name="twitter:title" concontent="<?php echo get_the_title();?>" />
<meta name="twitter:description" content="פודי - המתכונים הכי טעימים של השפים והבלוגרים המובילים והכתבות הכי מעניינות. FOODY אתר האוכל הגדול בישראל - היכנסו לאתר לפרטים נוספים ובתאבון!" />
<meta name="twitter:image" content="<?php echo $get_the_post_thumbnail_url;?>" />
