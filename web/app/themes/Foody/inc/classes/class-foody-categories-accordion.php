<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 12/11/18
 * Time: 11:16 AM
 */
class Foody_CategoriesAccordion {

	private $theme_location = 'categories';
	private $depth = 3;
	public $container_class = 'categories-accordion-widget show';
	public $container_id = 'accordion-categories-widget-accordion';
	public $menu_id = 'categories-widget-accordion-menu';
	public $menu_class = 'categories-widget-accordion-menu';

	/**
	 * Foody_CategoriesAccordion constructor.
	 */
	public function __construct() {
	}


	public function the_menu() {

		$title = foody_get_menu_title( $this->theme_location );

		?>
        <section class="sidebar-categories sidebar-section">
        <h2 class="title main-title">

            <a href="#<?php echo $this->container_id ?>" data-toggle="collapse" aria-expanded="true"
               aria-controls="<?php echo $this->container_id ?>">
                <i class="icon-categories"></i>
                <span>
                <?php echo $title ?>
            </span>

                <i class="icon-side-arrow"></i>

            </a>
        </h2>

		<?php

		$nav_args = $this->get_menu_args();


		wp_nav_menu( $nav_args );

		echo '</section>';
	}


	public function get_menu_args() {


		return [
			'theme_location'    => $this->theme_location,
			'depth'             => $this->depth,
			'container'         => 'div',
			'container_class'   => $this->container_class,
			'container_id'      => $this->container_id,
			'menu_class'        => $this->menu_class,
			'menu_id'           => $this->menu_id,
			'fallback_cb'       => 'Foody_BootstrapAccordionNavwalker::fallback',
			'before_menu'       => '',
			'walker'            => new Foody_BootstrapAccordionNavwalker(),
			'item_class'        => 'category-accordion-item',
			'parent_item_class' => 'category-accordion-toggle'
		];
	}
}