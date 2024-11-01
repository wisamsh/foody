<?php echo $FoodyHeader_NewSite->GetMainHeader() ; ?>
  <div class="container-fluid menu_container">
 
  <?php
$menu = new Custom_Menu_Structure( 'primary' );
echo $menu->display_menu_tree( null, 'site_nav', 'nested_menu' );
// wp_nav_menu(array(
//   'theme_location' => 'primary', // Name from register_primary_menu()
//   'menu_class' => 'newsitenav',     // Class for the <ul> element
//   'container' => 'nav',               // Optional: wraps the menu in a <nav> element
//   'container_class' => 'menu_container', // Optional: class for the container
//   'depth' => 3,                       // Allows for up to three levels
//   'walker' => new WP_Bootstrap_Navwalker(), // Optional: use a custom walker if needed
// ));
?>

</div>