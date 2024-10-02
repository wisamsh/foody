<div class="container-fluid text-center primary_menu_container">
    <div class="row">
      <div class="col">
      Column
      </div>
      <div class="col">
        Column
      </div>
      <div class="col">
        Column
      </div>
    </div>
  </div>
  <div class="container menu_container">
  
  <?php
$menu = new Custom_Menu_Structure( 'primary' );
echo $menu->display_menu_tree( null, 'newsitenav' );
?>

</div>