<?php

use Handlebars\Handlebars;

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 6:17 PM
 */
class SidebarFilter {

	const FILTER_OPTIONS_ID = 'foody_search_options';
	const FILTER_SETTINGS_SELECTOR = 'groups';

	const FILTER_SECTIONS_SELECTOR = 'sections';

    private static $instance;
	private $engine;

	private $filters_post_id = self::FILTER_OPTIONS_ID;

	/**
	 * SidebarFilter constructor.
	 */
	public function __construct() {
		$this->engine = new Handlebars;
	}

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new SidebarFilter();
        }

        return self::$instance;
    }


	public function get_filter() {
		return $this->the_filter( false );
	}

	public function load_filters_id() {
		// load filters specific to current page
		$this->filters_post_id = get_filters_id();
	}

	public function the_filter( $echo = true ) {
		$this->load_filters_id();
		$title        = get_field( 'title', $this->filters_post_id );
		$accordion_id = 'foody-filter';

		$main_accordion_args = array(
			'title'         => $title,
			'id'            => $accordion_id,
			'content'       => $this->get_accordion_content(),
			'return'        => ! $echo,
			'title_classes' => 'main-title filter-title',
			'title_icon'    => 'icon-filter'
		);

		return foody_get_template_part(
			get_template_directory() . '/template-parts/common/accordion.php',
			$main_accordion_args
		);
	}


	/**
	 * The same as @param array $list
	 * @return bool|string
	 * @see the_list() but
	 * returns the content instead of displaying it.
	 */
	private function get_list( $list ) {
		return $this->the_list( $list, false );
	}

	/**
	 * @param array $list
	 * @param bool $echo
	 *
	 * @return bool|string
	 */
	private function the_list( $list, $echo = true ) {

		$title = $list['title'];
		$type  = $list['type'];


		$accordion_args = array(
			'title'   => $title,
			'id'      => $type . '-' . uniqid(),
			'content' => ''
		);

		$template = " <div class=\"md-checkbox\">
                    <input id=\"{{id}}\" type=\"checkbox\" name=\"{{id}}\"  data-exclude=\"{{exclude}}\" data-value=\"{{value}}\" data-type=\"{{type}}\">
                    <label for=\"{{id}}\">
                        {{label}}
                    </label>
                </div>";

		foreach ( $list['checkboxes'] as $checkbox ) {
			$item = $this->engine->render( $template, array(
				'id'      => $accordion_args['id'] . '_' . $checkbox['value'] . '_' . $checkbox['exclude'],
				'exclude' => $checkbox['exclude'],
				'value'   => $checkbox['value'],
				'type'    => $checkbox['type'],
				'label'   => $checkbox['title']
			) );

			$accordion_args['content'] .= $item;

		}

		$accordion_args['return'] = ! $echo;

		return foody_get_template_part(
			get_template_directory() . '/template-parts/common/accordion.php',
			$accordion_args
		);
	}


	/**
	 *  TODO document!
	 * @return string
	 */
	public function get_accordion_content() {
		$content = '';
		if ( have_rows( 'filters_list', $this->filters_post_id ) ) {

			// a list of filtering sections
			// as configured in Foody Search Options page
			// in the admin

           if ('foody_feed_channel' === get_post_type()) {
               $blocks = get_field( 'blocks', get_the_ID() );
               $count_manual=0;
               $count_dynamic=0;
               foreach ($blocks as $block) {
                   if ($block['type'] === 'manual' ) {
                       $count_manual ++;
                   }
                   if ($block['type'] === 'dynamic'){
                       $count_dynamic ++;
                   }
               }
                if ( $count_manual = 1  ) {
                    if ( $count_dynamic === 0 ) {
                        if ( get_field('filters_list',get_the_ID()) ) {
                            $filters_list = get_field( 'filters_list', get_the_ID() );
                        } else {
                            $filters_list = get_field( 'filters_list',$this->filters_post_id );
                        }
                    }

                }

            } else {
               if ( is_category() ) {
                   $referer= $_GET['referer'];
                   if ( !empty(get_field( 'filters_list', $referer ))) {
                       $filters_list = get_field( 'filters_list', $referer );
                   } else {
                       $filters_list = get_field( 'filters_list', $this->filters_post_id );
                   }

               } else {
                   $filters_list = get_field( 'filters_list', $this->filters_post_id );
               }


            }



			$lists = array_map( function ( $list ) {

				// type of the section
				// see available types
				// in class-foody-search.php
				$type = $list['type'];

				// title to show on top
				// of this section
				$list_title = $list['title'];


				$checkboxes = self::parse_search_args( $list );

				return [
					'title'      => $list_title,
					'checkboxes' => $checkboxes,
					'type'       => $type
				];

			}, $filters_list );

			foreach ( $lists as $list ) {
				$content .= $this->get_list( $list );
			}
		}

		return $content;
	}


	public static function parse_search_args_array( $lists ) {
        if( !empty($lists) ){
            $lists = array_map( 'SidebarFilter::parse_search_args', $lists );
            $types = [];
            foreach ( $lists as $list ) {
                foreach ( $list as $item ) {
                    $types[] = $item;
                }
            }

            return $types;
        }

	}

	/**
	 * @param array $list
	 * @param SidebarFilter $_this
	 *
	 * @return array filter items
	 */
	public static function parse_search_args( $list ) {

		// type of the section
		// see available types
		// in class-foody-search.php
		$type = $list['type'];

		// the items in this section
		$values = is_array($list['values']) ? $list['values'] : [];

		$exclude_all = $list['exclude_all'];

		return array_map( function ( $value_arr ) use ( $type, $exclude_all ) {

			$exclude = $value_arr['exclude'] || $exclude_all;

			$exclude = $exclude ? 'true' : 'false';

			$checkbox_item = [
				'type'    => $type,
				'value'   => $value_arr['value'],
				'exclude' => $exclude,
				'title'   => $value_arr['title']
			];

			if ( isset( $value_arr['switch_type'] ) ) {
				$switch_type = $value_arr['switch_type'];

				if ( $switch_type ) {
					$item_type              = $value_arr['value_group'];
					$checkbox_item['type']  = $item_type['type'];
					$checkbox_item['value'] = $item_type['value'];
				}
			}


			if ( empty( $checkbox_item['title'] ) ) {
				$checkbox_item['title'] = self::get_item_title( $checkbox_item['value'], $checkbox_item['type'] );
			}

			return $checkbox_item;
		}, $values );
	}

	/**
	 * @param $id
	 * @param $type
	 *
	 * @return null|string|WP_Error
	 */
	public static function get_item_title( $id, $type ) {

		$title = '';
		switch ( $type ) {
			case 'categories':
			case 'tags':
			case 'limitations':
				$title = get_term_field( 'name', $id, self::type_to_taxonomy( $type ) );
				if ( is_wp_error( $title ) ) {
					$title = '';
				}
				break;

			case 'ingredients':
			case 'accessories':
			case 'techniques':
				$title = get_the_title( $id );
				break;
			case 'authors':
				$title = get_the_author_meta( 'display_name', $id );
				break;
		}

		return $title;
	}

	/**
	 * @param $type
	 *
	 * @return string
	 */
	private static function type_to_taxonomy( $type ) {
		$tax = '';
		switch ( $type ) {
			case 'categories':
				$tax = 'category';
				break;
			case 'tags':
				$tax = 'post_tag';
				break;
			case 'limitations':
				$tax = 'limitations';
				break;
		}

		return $tax;
	}

}

