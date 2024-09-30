<?php

//Wisam Code Checking Redirection : 
$CheckRedirectionPage = new CheckRedirectionPage;
print_r($CheckRedirectionPage->GetRedirectionPages());
$header = new Foody_Header();
$user = new Foody_User();
$user_param = is_user_logged_in() ? $user->user->ID : 'false';
$show_accessibility = get_theme_mod('foody_show_accessibility') || get_theme_mod('show_white_label_accessibility');
$google_site_verification_id = get_option('foody_google_site_verification_id', false);
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
  <?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <?php } ?>
  <?php if ($google_site_verification_id) { ?>
    <meta name="google-site-verification" content="<?php echo $google_site_verification_id; ?>" />
  <?php } ?>
  <meta name="theme-color" content="#ffffff">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <?php
  //Wisam : this fo white labels only - meta description and meta keywords======================
  // if ($_SERVER['HTTP_HOST'] != "foody.co.il") {
  //   require(__DIR__ . '/w_helpers/whitelabels_category-seo.php');
  // }
  //End meta for whitlabels=====================================================================
  ?>
  <?php Foody_Header::google_tag_manager(); 
  require_once(get_template_directory() . '/ns_partials/caringoren.php');
  ?>
  <?php wp_head(); ?>
</head>

<?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
  <script src="https://public.bankhapoalim.co.il/bitcom/sdk"></script>
<?php } ?>

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

  <div id="fb-root"></div>
  <?php Foody_Header::google_tag_manager_iframe(); ?>

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
  <div id="page" class="container-fluid site">
    <?php $post_type = is_single() && isset($post) && isset($post->post_type) ? $post->post_type : ''; ?>
    <header id="masthead" class="site-header no-print <?php if ($post_type == 'foody_recipe' && wp_is_mobile()) {
                                                        echo 'hidden-recipe-header';
                                                      } ?>">
      <!-- #site-navigation -->
    </header><!-- #masthead -->


    <?php if (is_single() && $post_type == 'foody_recipe') {
      // add header for print
      Foody_Header::getPrintHeader();
    } ?>
    <?php
    if (get_page_template_slug() === 'page-templates/homepage.php') {
      $brands_avenue_group = get_field('brands_avenue', 'foody_brands_avenue');
      if (isset($brands_avenue_group['brands']) && !empty($brands_avenue_group['brands'])) { ?>

    <?php
      }
    }
    ?>
<header>
<div class="container text-center">
  <div class="row">
    <div class="col">
      TODO UPLOAD PHOTOS TO S3BUCKET IN AWS FOR CDN
    </div>
    <div class="col">
      Column
    </div>
    <div class="col">
      Column
    </div>
  </div>
</div>
      </header>
    <div id="content" class="site-content">
      <?php

      //Wisam Manual scripts : 

      require_once(get_template_directory() . "/w_helpers/foody-manual-scripts.php");

      ?>
      