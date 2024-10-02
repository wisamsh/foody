<?php

//Wisam Code Checking Redirection : 
$CheckRedirectionPage = new CheckRedirectionPage;
print_r($CheckRedirectionPage->GetRedirectionPages());
$google_site_verification_id = get_option('foody_google_site_verification_id', false);
$FoodyHeader_NewSite = new FoodyHeader_NewSite;
//LazyLoadImage

?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">

<head>
  <?php // for facebook metatags
  //require (__DIR__ . '/w_helpers/taboola_in_head.php');
  ?>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
  <?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <?php } ?>
  <?php if ($google_site_verification_id) { ?>
    <meta name="google-site-verification" content="<?php echo $google_site_verification_id; ?>" />
  <?php } ?>
  <meta name="theme-color" content="#ffffff">
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>
  <?php if (get_post_type() == 'questions') {
    echo 'itemscope itemtype="https://schema.org/QAPage"';
  } ?> dir="rtl">
  <?php

  if (get_post_type() != 'poll' && !wp_is_mobile() && (isset($_SESSION['background_image']) && !empty($_SESSION['background_image']))) {
  ?>
    <img class="body-background no-print" src="<?php echo $_SESSION['background_image']['url'] ?>"
      alt="<?php echo $_SESSION['background_image']['alt'] ?>">
  <?php
    unset($_SESSION['background_image']);
  }
  ?>


  <?php if (!empty($edit_link = get_edit_post_link()) && !wp_is_mobile()): ?>
    <div dir="rtl" style="text-align: right; max-width: 960px;margin: 0 auto;position: relative;">
      <a href="<?php echo $edit_link ?>"><?php echo __('ערוך') ?></a>
      <?php
      //if there is google photo:
      if (get_field('google_photo_link', get_the_ID())) {
        $googlephoto = get_field('google_photo_link', get_the_ID());
        echo ('<a href="' . $googlephoto . '" target="_blank">לתמונת המתכון בגוגל פוטו</a>');
      }
      ?>
    </div>
  <?php endif; ?>


  <div class="container-fluid text-center">
    <div class="row">
      <div class="col">
        <?php 
        
       echo $FoodyHeader_NewSite->LazyLoadImage('https://images.pexels.com/photos/1624496/pexels-photo-1624496.jpeg', '');
        ?>
      </div>
      <div class="col">
        Column
      </div>
      <div class="col">
        Column
      </div>
    </div>
  </div>

  <div id="page" class="container-fluid site">
    <div id="content" class="site-content">
      <?php

      //Wisam Manual scripts : 

      require_once(get_template_directory() . "/w_helpers/foody-manual-scripts.php");

      ?>