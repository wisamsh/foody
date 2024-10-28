<?php echo $FoodyHeader_NewSite->GetMainHeader() ; ?>
  <div class="container-fluid menu_container">
 
  <?php
$menu = new Custom_Menu_Structure( 'primary' );
echo $menu->display_menu_tree( null, 'newsitenav', 'nested_submenu' );
?>

</div>