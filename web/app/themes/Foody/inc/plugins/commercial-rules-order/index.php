<?php
/**
 * Plugin Name: Foody Commercial Rules Ordering
 * Plugin URI: http://changeset.hr/
 * Description: Order your users using drag and drop on the built in page list.
 * Version: 0.2
 * Author: Fran Hrženjak
 * Author URI: http://changeset.hr
 * License: GPLv2 or later
 */


class FoodyCommercialRulesOrdering_Plugin {

	static protected $instance = null;

	static public function load() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function should_rules_kick_in() {
		if ( ! is_admin() ) {
			return false;
		}
		$screen = get_current_screen();
		if ( empty( $screen ) || ! empty( $screen ) && $screen->id !== 'edit-foody_comm_rule' ) {
			return false;
		}
		if ( isset( $_GET['orderby'] ) ) {
			return false;
		}

		return true;
	}

	protected function __construct() {
		add_action( 'pre_get_posts', array( $this, 'alter_rules_search' ) );
		add_action( 'admin_head', array( $this, 'sort_rules_js' ) );
		add_action( 'wp_ajax_change_rule_order', array( $this, 'ajax_rule_update' ) );
		add_action( 'save_post_foody_comm_rule', array( $this, 'set_rule_order_save' ) );
		add_filter( 'manage_foody_comm_rule_posts_columns', array( $this, 'add_foody_comm_rule_id_column' ) );
		add_action( 'manage_foody_comm_rule_posts_custom_column', array(
			$this,
			'show_menu_order_column_content'
		), 10, 2 );
//		add_filter( 'manage_foody_comm_rule_posts_columns', array( $this, 'hide_foody_comm_rule_id_column' ) );
		add_filter( 'views_foody_comm_rule', array( $this, 'sort_by_order_link' ) );
	}


	function alter_rules_search( $qry ) {

		if ( ! $this->should_rules_kick_in() ) {
			return;
		}

		if ( is_admin() ) {
			/** @var $qry WP_Query */
			$qry->set( 'orderby', 'meta_value' );
			$qry->set( 'meta_key', 'menu_order' );
			$qry->set( 'order', 'ASC' );
		}
	}


	function sort_rules_js() {

		if ( ! $this->should_rules_kick_in() ) {
			return;
		}
		wp_enqueue_script( 'jquery-ui', '//code.jquery.com/ui/1.10.3/jquery-ui.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'jquery-ui-css', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
		?>
        <script type="text/javascript">
            jQuery(function ($) {

                jQuery.fn.reverse = [].reverse;

                var update_orders = function () {

                    var $the_list = $('#the-list');
                    $the_list.addClass('updating').sortable("disable").css('cursor', 'no-drop');

                    // normalize values:
                    // force values to int (make "0" if missing)
                    $the_list.find('tr').each(function (i, item) {
                        var $tr = $(item);
                        var $menu_order_td = $tr.find('.column-menu_order');

                        $menu_order_td.text(parseInt('0' + $menu_order_td.text()));
                    });


                    // do a one-pass bubble sort and mark updated needed
                    var $last_tr = null;
                    $the_list.find('tr').each(function (i, item) {
                        var $tr = $(item);
                        var $menu_order_td = $tr.find('.column-menu_order');
                        if ($last_tr !== null) {
                            var $last_menu_order_td = $last_tr.find('.column-menu_order');
                            if (parseInt($last_menu_order_td.text()) >= parseInt($menu_order_td.text())) {
                                var tmp = $menu_order_td.text();
                                $menu_order_td.text($last_menu_order_td.text());
                                $last_menu_order_td.text(tmp);
                                $last_tr.addClass('update_menu_order_needed');
                                $tr.addClass('update_menu_order_needed');
                            }
                        }
                        $last_tr = $tr;
                    });


                    // do a one-pass bubble sort in the oposite direction and mark updated needed
                    $last_tr = null;
                    $the_list.find('tr').reverse().each(function (i, item) {
                        var $tr = $(item);
                        var $menu_order_td = $tr.find('.column-menu_order');
                        if ($last_tr !== null) {
                            var $last_menu_order_td = $last_tr.find('.column-menu_order');
                            if (parseInt($last_menu_order_td.text()) < parseInt($menu_order_td.text())) {
                                var tmp = $menu_order_td.text();
                                $menu_order_td.text($last_menu_order_td.text());
                                $last_menu_order_td.text(tmp);
                                $last_tr.addClass('update_menu_order_needed');
                                $tr.addClass('update_menu_order_needed');
                            }
                        }
                        $last_tr = $tr;
                    });

                    // make sure values are unique, increase where needed
                    $last_tr = null;
                    $the_list.find('tr').each(function (i, item) {
                        var $tr = $(item);
                        var $menu_order_td = $tr.find('.column-menu_order');
                        if ($last_tr !== null) {
                            var $last_menu_order_td = $last_tr.find('.column-menu_order');
                            // do a one-pass bubble sort
                            if (parseInt($last_menu_order_td.text()) === parseInt($menu_order_td.text())) {
                                $menu_order_td.text(parseInt($last_menu_order_td.text()) + 1);
                                $tr.addClass('update_menu_order_needed').css('font-weight', 'bold');
                            }
                            $menu_order_td.text(parseInt('0' + $menu_order_td.text()));
                        }
                        $last_tr = $tr;
                    });

                    // collect data for AJAX based on update_menu_order_needed class
                    // also add spinner elements
                    var update_data = {};
                    $the_list.find('tr.update_menu_order_needed').each(function (i, item) {
                        var $tr = $(item);
                        var post_id = $tr.attr('id').replace('post-', '');
                        var $menu_order_td = $tr.find('.column-menu_order');
                        update_data[post_id] = $menu_order_td.text();
                        $tr.find('th:first-child')
                            .filter(':not(:has(.relative_wrap))')
                            .wrapInner('<div class="relative_wrap" style="position: relative;"></div>')
                            .end()
                            .find('.spinner')
                            .remove()
                            .end()
                            .find('.relative_wrap')
                            .append('<span class="spinner" style="display: block; position: absolute; top: -5px; left: 1px;"></span>')
                        ;
                    });

                    var data = {
                        action: 'change_rule_order',
                        update_data: update_data
                    };

                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    //noinspection JSUnresolvedVariable
                    $.post(ajaxurl, data, function () {
                        $the_list
                            .find('.spinner')
                            .remove()
                            .end()
                            .find('.update_menu_order_needed')
                            .removeClass('update_menu_order_needed')
                            .end()
                            .removeClass('updating')
                            .sortable("enable")
                            .css('cursor', 'move')
                        ;
                    });

                };

                $('#the-list:not(.ui-sortable)')
                    .css('cursor', 'move')
                    .sortable({
                        start: function (event, ui) {
                            ui.item.addClass('alternate');
                            ui.item.width(ui.helper.width());
                            ui.helper.find('th, td').each(function (i, item) {
                                var w = ui.item.parent().parent().find('thead').find('th').eq(i).width();
                                $(item).width(w);
                            });
                            ui.placeholder.css('display', 'table-row');
                            ui.placeholder.height(ui.helper.height());
                            ui.placeholder.width(ui.helper.width());
                            ui.item.parent().parent().parent().find('tr:not(.ui-sortable-helper)').find('th:hidden, td:hidden')
                                .css({
                                    'visibility': 'visible',
                                    'display': 'table-cell',
                                    'overflow': 'hidden',
                                    'width': 0,
                                    'max-width': 0,
                                    'padding': 0,
                                    'white-space': 'nowrap'
                                })
                            ;
                        },
                        update: function () {
                            $(this).find('tr')
                                .removeClass('alternate')
                                .filter(':even')
                                .addClass('alternate')
                            ;
                            update_orders();
                        }
                    })
                ;
            });
        </script>
		<?php
	}


	function ajax_rule_update() {
		$update_data = (array) $_POST['update_data'];

		foreach ( $update_data as $post_id => $new_order ) {
			update_post_meta( $post_id, 'menu_order', (int) $new_order );
		}

		die(); // this is required to return a proper result
	}


	function set_rule_order_save( $post_id ) {
		$post = get_post( $post_id );
		if ( $post->post_type != 'revision' && $post->post_status != 'auto-draft' ) {
			$query = new WP_Query( [ 'post_type' => [ 'foody_comm_rule' ] ] );
			$meta  = get_post_meta( $post_id, 'menu_order' );
			if ( empty( $meta ) ) {
				update_post_meta( $post_id, 'menu_order', $query->found_posts );
			}
		}
	}


	function add_foody_comm_rule_id_column( $columns ) {
		$columns['menu_order'] = 'Order';

		return $columns;
	}

	function show_menu_order_column_content( $column_name, $post_id ) {

		if ( 'menu_order' == $column_name ) {
			echo get_post_meta( $post_id, 'menu_order', true );
		}

	}

//
//	function hide_foody_comm_rule_id_column( $columns ) {
//		unset( $columns['menu_order'] );
//
//		return $columns;
//	}

	function sort_by_order_link( $views ) {
		$orderby          = isset( $_GET['orderby'] ) ? $_GET['orderby'] : '';
		$class            = empty( $orderby ) ? 'current' : '';
		$query_string     = remove_query_arg( array( 'orderby', 'order' ) );
		$views['byorder'] = '<a href="' . $query_string . '" class="' . $class . '">Sort by Order</a>';

		return $views;
	}


}

global $foody_commercial_rules_ordering_plugin;
$foody_commercial_rules_ordering_plugin = FoodyCommercialRulesOrdering_Plugin::load();
