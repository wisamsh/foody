<?php // need to change app_id to 242005383411645 on production?>
<?php $domain = array(
"foody.moveodevelop.com" => "300250737432710",
"foody.co.il" => "242005383411645",
"foody-local.co.il" => "123456"
);
$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>

<meta property="fb:app_id"             content="<?php echo $domain[$_SERVER['HTTP_HOST']];?>" />
<meta property="og:url"                content="<?php echo $actual_link ;?>" />
<meta property="og:type"               content="article" />
<meta property="og:title"              content="<?php echo get_the_title();?>" />
<meta property="og:description"        content="פודי - המתכונים הכי טעימים של השפים והבלוגרים המובילים והכתבות הכי מעניינות. FOODY אתר האוכל הגדול בישראל - היכנסו לאתר לפרטים נוספים ובתאבון!" />
<meta property="og:image"              content="https://foody-media.s3.eu-west-1.amazonaws.com/w_images/m_logofoody_2.jpg" />
