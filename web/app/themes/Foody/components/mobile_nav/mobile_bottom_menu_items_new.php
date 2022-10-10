<?php
 Foody_Header::getPrintHeader();
class AWP_Menu_Walker extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth=0, $args=[], $id=0) {
		$output .= "<li class='menu_item_top'> <span id='".$item->ID."' onclick='swapme(`".$item->ID."`);' class='plus_btn'>+</span>";

		if ($item->url && $item->url != '#') {
			$output .= '<a href="' . $item->url . '">';
		} else {
			$output .= '<span>';
		}

		$output .= $item->title;

		if ($item->url && $item->url != '#') {
			$output .= '</a>';
		} else {
			$output .= '</span>';
		}
	
	
	
	
	}
	
	
	
}
$new_menu_args = array(
'menu'=>'Navbar',
'menu_class'=>'navbar-nav',
'menu_id'=>'foody_mobile_menu',
'container'=> 'nav',
'container_class'=>'',
'container_id'=>'quadmenu_new',
'depth'=>'2',
'items_wrap'=>'<ul class="quadmenu-navbar-nav">%3$s</ul>',
'walker'=> new AWP_Menu_Walker()

);
 $BottomMenu = wp_nav_menu($new_menu_args);
 echo $BottomMenu;
?>
