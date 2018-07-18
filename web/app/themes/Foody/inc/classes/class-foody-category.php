<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/2/18
 * Time: 3:34 PM
 */
class Foody_Category {
	public $id, $link, $title, $categories, $name, $category;

	/**
	 * MalamSubCategory constructor.
	 * @param int $id
	 * @param string $category_name
	 */
	public function __construct($id = false, $categories = null)
	{
		$this->id = 0;
		if ($categories != null) {
			$this->categories = $categories;
		} else {
			$this->categories = get_the_category($id);
		}

		if($id){
		    $this->category = get_term($id);
        }

		wp_reset_query();
	}

	/**
	 * @return mixed
	 */
	public function parent($title = null, $id = null)
	{

		$locations = get_nav_menu_locations();
		$menu = get_term($locations['primary'], 'nav_menu');
		$menu_items = wp_get_nav_menu_items($menu->slug);

		$parent_term_id = 0;

		// in case the post is associated with
		// several categories we need to make sure
		// the correct category is selected to be shown
		// in the breadcrumbs so we have to compare
		// the current category title
		// with the possible categories
		if ($title != null) {

			foreach ($this->categories as $c) {
				if (strcmp(mb_strtolower(mb_convert_encoding($c->name, 'UTF-8', 'auto'), 'UTF-8'), mb_strtolower($title, 'UTF-8')) === 0) {
					$this->category = $c;
					break;
				}
			}

			if (isset($this->category)) {

				$this->id = $this->category->cat_ID;
			} elseif ($id != null) {
				$this->id = $id;
			}
		} else {
			$this->category = $this->categories[0];
			$this->id = $this->category->cat_ID;


		}


		foreach ($menu_items as $menu_item) {
			//  echo $menu_item->object_id.' - '.$this->id. "
			//";

			if ($menu_item->object_id == $this->id) {


				$parent_term_id = $menu_item->menu_item_parent;

				break;
			}

		}
		foreach ($menu_items as $menu_item) {
			if ($menu_item->ID == $parent_term_id) {
				return $menu_item->object_id;
				break;
			}
		}

		wp_reset_query();

	}


	public function get_category_id()
	{

	}


    /**
     * Get the category image (ACF Field)
     * @return mixed|null|string
     */
    public function get_image(){
	    $image = '';
	    if($this->category != null){
            $image = get_field( 'image', $this->category->taxonomy . '_' . $this->category->term_id );
        }

        return $image;
    }
}