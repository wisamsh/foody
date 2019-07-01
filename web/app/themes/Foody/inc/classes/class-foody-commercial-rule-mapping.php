<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/24/19
 * Time: 7:00 PM
 */

class Foody_CommercialRuleMapping
{
    public static $table_name;

    public static function createTable()
    {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'foody_commercial_rule_mapping';
	    $charset_collate = $wpdb->get_charset_collate();

	    $sql = "CREATE TABLE " . self::$table_name . "  (
              `ID` BIGINT(20) NOT NULL AUTO_INCREMENT,
              `rule_id` BIGINT(20) NOT NULL,
              `recipe_id` BIGINT(20) NOT NULL,
              `object_id` BIGINT(20) NOT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`ID`)) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function add($rule_id, $recipe_id, $object_id)
    {
        global $wpdb;
        $result = $wpdb->insert(self::$table_name, [
            'rule_id' => $rule_id,
            'recipe_id' => $recipe_id,
            'object_id' => $object_id
        ]);

        if ($result === false) {
            Foody_WhiteLabelLogger::error("Error inserting to foody_commercial_rule_mapping: $wpdb->last_error", $wpdb->last_result);
        }

        return $result;
    }

	public static function remove( $id ) {
		global $wpdb;

		return $wpdb->delete( self::$table_name, [
			'id' => $id
		] );
	}

	public static function removeRules( $rule_id ) {
		global $wpdb;

		return $wpdb->delete( self::$table_name, [
			'rule_id' => $rule_id,
		] );
	}

	public static function getByRecipe( $recipe_id ) {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT * from " . self::$table_name . " where recipe_id = " . $recipe_id . "ORDER BY DATE(created_at) DESC, created_at ASC", ARRAY_A );

		return $results;
	}

	public static function getByIngredientRecipe( $recipe_id, $object_id ) {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT * from " . self::$table_name . " where object_id = " . $object_id . " AND recipe_id = " . $recipe_id . "ORDER BY DATE(created_at) DESC, created_at ASC", ARRAY_A );

		return $results;
	}

}

/**
 * @param $post_id
 * @param $post
 * @param $update
 */
function foody_save_commercial_rule_mapping( $rule_id, $rule, $update ) {

	if ( get_post_type() == 'foody_comm_rule' ) {

		// Clear old rules
		Foody_CommercialRuleMapping::removeRules($rule_id);

		$posts = [];

		if ( get_field( 'type' ) == 'area' ) {
			// Area rule

			$areas = get_field( 'comm_rule_area' );

			// loop over areas to fetch relevant recipes
			foreach ( $areas as $area ) {
				$foody_search  = new Foody_Search( 'feed_channel' );
				$foody_query   = Foody_Query::get_instance();
				$blocks_drawer = new Foody_Blocks( $foody_search );

				$blocks = get_field( 'blocks', $area->ID );

				if ( ! empty( $blocks ) ) {

					foreach ( $blocks as $block ) {
						$type = $block['type'];

						if ( ! empty( $type ) ) {
							if ( $type == 'dynamic' ) {
								$blocks_drawer->validate_block( $block );

								$block_fn = "get_{$type}_block_posts";
								if ( method_exists( $blocks_drawer, $block_fn ) ) {
									$block_posts = call_user_func( [ $blocks_drawer, $block_fn ], $block );
									if ( ! empty( $block_posts ) ) {
										$posts = array_merge( $posts, $block_posts );
									}
								}
							} else if ( $type == 'manual' ) {
								if ( ! empty( $block['items'] ) ) {
									$block_posts = [];
									foreach ( $block['items'] as $item ) {
										if ( ! empty( $item ) && ! empty( $item['post'] ) ) {
											array_push( $block_posts, $item['post'] );
										}
									}
									$posts = array_merge( $posts, $block_posts );
								}
							}
						}
					}
				}

				//
			}
		} else if ( get_field( 'type' ) == 'filter' ) {
			// Filter rule

			$filter = get_field( 'comm_rule_filter' );


			$filters = [];

			// consider all filters lists
			foreach ($filter['filters_list'] as $list){
				if (is_array($list)){
					$filters = array_merge($filters,$list);
				}
			}

			$args = [
				'types' => SidebarFilter::parse_search_args($filters)
			];

			// purchase_buttons will invoke purchase_buttons ffn
			// in class Foody_Query
			$foody_search = new Foody_Search('foody_commercial_rule', $filters);

			$result = $foody_search->query($args);
			// Execute Rule
			$posts = array_merge( $posts, $result['posts'] );
			// TODO
		}
		$object_type = get_field( 'object_type' );
		$object      = get_field( 'object' );

		$posts = array_unique( $posts, SORT_REGULAR );

		// Save new rule mapping - TODO: foreach recipe id
		foreach ( $posts as $post ) {
			Foody_CommercialRuleMapping::add( $rule_id, $post->ID, $object->ID );
		}
	}

}

Foody_CommercialRuleMapping::createTable();

add_action( 'save_post', 'foody_save_commercial_rule_mapping', 10, 3 );