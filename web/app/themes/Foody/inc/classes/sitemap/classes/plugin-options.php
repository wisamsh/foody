<?php

/**
 * Plugin options class.
 *
 * Handles all the functionality for plugin options.
 *
 * @since 0.1.0
 */
class WPGO_Simple_Sitemap_Pro_Options {

	protected $_plugin_options_page;
	protected $_args; // handle to the plugin options page

	/**
	 * Plugin options class constructor.
	 */
	public function __construct($args) {

		$this->_args = $args;

		add_action( 'admin_init', array( &$this, 'register_plugin_settings' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
		add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_front_end_scripts' ) );

		add_filter( 'simple_sitemap_pro_defaults', array( &$this, 'add_defaults' ) );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_settings_link' ), 10, 2 ); // add 'Settings' link on main Plugins page
	}

	/**
	 * Register plugin options page, and enqueue scripts/styles.
	 *
	 * @since 0.1.0
	 */
	public function add_options_page() {

		$this->_plugin_options_page = add_options_page(
			$this->_args['plugin_name'] . " Options Page",
			$this->_args['plugin_name'] ,
			'manage_options',
			$this->_args['menu_slug'],
			array( &$this, 'render_plugin_form' )
		);

		/* Enqueue scripts and styles for the plugin option page */
		add_action( "admin_print_scripts-$this->_plugin_options_page", array( &$this, 'plugin_admin_scripts' ) );
		add_action( "admin_print_styles-$this->_plugin_options_page", array( &$this, 'plugin_admin_styles' ) );
	}

	/**
	 * Get plugin option default settings.
	 *
	 * @since 0.1.0
	 */
	public static function get_default_plugin_options() {

		$defaults = array();

		// setup an array to store list of checkboxes that have a checkbox default set to 1
		$defaults["default_on_checkboxes"] = array();

		/* Add plugin specific default settings via this filter hook. */
		return WPGO_Simple_Sitemap_Pro_Hooks::simple_sitemap_pro_defaults($defaults);
	}

	/**
	 * Get current plugin options.
	 *
	 * Merges plugin options with the defaults to ensure any gaps are filled.
	 * i.e. when adding new options.
	 *
	 * @since 0.1.0
	 */
	public static function get_plugin_options() {

		$options = get_option( WPGO_SIMPLE_SITEMAP_PRO_OPTIONS_DB_NAME );
		$defaults = self::get_default_plugin_options();

		// store the OFF checkboxes array
		$default_on_checkboxes_arr = $defaults["default_on_checkboxes"];

		// remove the OFF checkboxes array from the main defaults array
		unset($defaults["default_on_checkboxes"]);

		if( is_array($options) ) {
			// merge OFF checkboxes into main options array to add entries for empty checkboxes
			$options = array_merge( $default_on_checkboxes_arr, $options );
		}

		return wp_parse_args(
			$options,
			$defaults
		);

		//return wp_parse_args(
		//	get_option( WPGO_SIMPLE_SITEMAP_PRO_OPTIONS_DB_NAME ),
		//	self::get_default_plugin_options()
		//);
	}

	/**
	 * Register plugin options with Settings API.
	 *
	 * @since 0.1.0
	 */
	public function register_plugin_settings() {

		// @todo DELETE this in future version?
	    /*if( !get_option('ssp_update_options') ) {
	        add_option('ssp_update_options', '1');
		    delete_option( $this->_args['options_db_name'], self::get_default_plugin_options() );
		    delete_option('ssp_update_options');
	    }*/

		/* Register plugin options settings for all tabs. */
		register_setting(
			$this->_args['options_group'],
			$this->_args['options_db_name'],
			array( $this, 'sanitize_plugin_options' )
		);

		/* Register plugin options section, to add individual fields. */
		add_settings_section( $this->_args['options_section'], '', '__return_false', $this->_args['menu_slug'] );

		/* Register plugin support fields. */
		add_settings_field(
			'wpgo_support_plugin_option',
			__( 'Support and Tutorials', 'wpgo-simple-sitemap-pro' ),
			array( $this, 'render_support_fields' ),
			$this->_args['menu_slug'],
			$this->_args['options_section']
		);

		/* Register wpgothemes.com user account page link fields. */
		add_settings_field(
			'wpgo_myaccount_plugin_option',
			__( 'WPGO Plugins Account', 'wpgo-simple-sitemap-pro' ),
			array( $this, 'render_myaccount_fields' ),
			$this->_args['menu_slug'],
			$this->_args['options_section']
		);
	}

	/**
	 * Sanitize plugin options.
	 *
	 * Get rid of the local license key status option when adding a new one
	 *
	 * @since 0.1.0
	 */
	public function sanitize_plugin_options( $input ) {

		// strip html from textboxes
		$input['txtar_sitemap_script'] = wp_filter_nohtml_kses( $input['txtar_sitemap_script'] );
		$input['txt_exclude_parent_pages'] = wp_filter_nohtml_kses( $input['txt_exclude_parent_pages'] );

		/* Sanitize plugin options via this filter hook. */
		// this allows you to sanitize options via another class
		//return WPGO_Simple_Sitemap_Pro_Hooks::wpgo_sanitize_plugin_options( $input );
		return $input;
	}

	/* Define default option settings. */
	public function add_defaults($defaults) {

		$defaults["txtar_sitemap_script"] = "";
		$defaults["chk_parent_page_link"] = "1";
		$defaults["txt_exclude_parent_pages"] = "";

		$defaults["default_on_checkboxes"]["chk_parent_page_link"] =  "0";

		return $defaults;
	}

	/**
	 * Render support fields.
	 *
	 * @since 0.1.0
	 */
	public function render_support_fields() {
	?>
		<div id="wpgo-buttons">
			<a class="button-secondary wpgo-lower" href="//wpgoplugins.com/document/simple-sitemap-pro-documentation/" target="_blank">Plugin Setup</a>
			<a class="button-secondary wpgo-lower" href="//wpgoplugins.com/premium-plugin-support/" target="_blank">Premium Support</a>
			<a style="padding-left:4px;padding-right:4px;" class="button-secondary wpgo-lower" href="https://twitter.com/wpgoplugins" target="_blank"><span style="padding-top:3px;color:#55acee;" class="dashicons dashicons-twitter" title="Join us on Twitter!"></span></a>
			<a style="padding-left:4px;padding-right:4px;" class="button-secondary wpgo-lower" href="https://www.facebook.com/WPGO-Plugins-555709481298765" target="_blank"><span style="padding-top:3px;color:#3b5998;" class="dashicons dashicons-facebook" title="Join us on Facebook!"></span></a>
		</div>
	<?php
	}

	/**
	 * Render support fields.
	 *
	 * @since 0.1.0
	 */
	public function render_myaccount_fields() {
	?>
		<div id="wpgo-myaccount-btn">
			<a class="button-secondary wpgo-lower" href="//wpgoplugins.com/my-account" target="_blank">Go To My Account</a>
			<p class="description">Access your wpgoplugins.com account page to view purchased items, view/renew license keys, download products, and update profile settings.</p>
		</div>
	<?php
	}

	/**
	 * Display plugin options page.
	 *
	 * @since 0.1.0
	 */
	public function render_plugin_form() {
		?>
		<div class="wrap">
			<h2 class="plugin-title"><?php printf( __( '%s Options', 'wpgo-simple-sitemap-pro' ), $this->_args['plugin_name'] ); ?></h2>

			<?php
			// Check to see if user clicked on the reset options button
			if ( isset( $_POST['reset_options'] ) ) :

				// Reset plugin defaults
				update_option( $this->_args['options_db_name'], self::get_default_plugin_options() );

				// Display update notice here
				?>
				<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
					<p><strong><?php echo 'Settings reset.'; ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
				</div>
				<?php

			endif;

			// set the active tab
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'simple_sitemap_pro';
			$cft = $spt = '';
			if( $active_tab == 'simple_sitemap_pro' )
			$cft = ' nav-tab-active';
			elseif( $active_tab == 'support' )
			$spt = ' nav-tab-active';
			?>

			<h2 class="nav-tab-wrapper">
				<a href="simple-sitemap-pro" class="nav-tab<?php echo $cft; ?>">Simple Sitemap Shortcodes</a>
				<a href="support" class="nav-tab<?php echo $spt; ?>">License & Support</a>
			</h2>

			<!-- Start Main Form -->
			<form id="plugin-options-form" method="post" action="options.php">
				<?php
				$options = self::get_plugin_options();

				settings_fields( $this->_args['options_group'] );
				?>

				<div class="simple-sitemap-pro-tab tab-content">
					<table class="form-table">

						<tr valign="top">
							<td colspan="2">
								<p>The Simple Sitemap Pro plugin includes two shortcodes with many attributes available to customize the output of your sitemap.</p>
								<p><strong>TIP: If you haven't used the plugin before start by entering <code>[simple-sitemap]</code> into any post/page without any attributes. Then, add attributes as necessary until you get the required results.</strong></p>

                                <br><label><input name="<?php echo $this->_args['options_db_name']; ?>[chk_parent_page_link]" type="checkbox" value="1" <?php if ( isset( $options['chk_parent_page_link'] ) ) {
										checked( '1', $options['chk_parent_page_link'] );
									} ?>> Remove parent page links?</label><br><br>

                                <input type="text" class="exclude regular-text code" name="<?php echo $this->_args['options_db_name']; ?>[txt_exclude_parent_pages]" value="<?php echo $options['txt_exclude_parent_pages']; ?>">
                                <p class="description">Enter comma separated list of parent page IDs to remove specific links. Leave blank to remove ALL parent page links.</p><br>

                                <div style="background:#fff;border: 1px dashed #ccc;padding: 15px;padding-top:5px;">
									<h2><?php _e( '[simple-sitemap] shortcode', 'simple-sitemap' ); ?></h2>

									<p>Use this shortcode to display a sitemap of different post types. Note: Using this shortcode with no attributes outputs a list of posts for each post type: <code>[simple-sitemap]</code></p>

									<p>There are many attributes you can use to customize the look of your sitemap. Attributes can be combined as required for an even more flexible sitemap layout.</p>

									<ol style="line-height:22px;">
										<li>Render the sitemap in a tabbed layout. Each tab contains a separate post type:
											<code>[simple-sitemap render='tab']</code></li>
										<li>Specify the type and order of post types:
											<code>[simple-sitemap types='post, page']</code>
											<p style="margin-top:8px;margin-bottom:12px;"><?php printf( __( 'Choose from any of the following registered post types currently available:', 'simple-sitemap' ) ); ?>
											<?php
											$post_type_args = array(
												'public'   => true
											);
											$registered_post_types = get_post_types($post_type_args);
											$registered_post_types_str = implode(', ', $registered_post_types);
											echo '<code>' . $registered_post_types_str . '</code>';
											?></p>
										</li>
										<li>Set page depth (i.e. indentation) for nested posts:
											<code>[simple-sitemap page_depth='1']</code></li>
										<li>Wrap each sitemap entry in a specific HTML tag:
											<code>[simple-sitemap title_tag='h3']</code></li>
										<li>Wrap the post type label in a specific HTML tag:
											<code>[simple-sitemap post_type_tag='h2']</code></li>
										<li>Order posts for each post type via title, date, author, ID, menu_order etc. See full list of options <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>: <code>[simple-sitemap orderby='title']</code></li>
										<li>Whatever the 'orderby' attribute is set to the 'order' attribute will sort them in either ascending or descending:
											<code>[simple-sitemap order='asc|desc']</code></li>
										<li>Optionally show a post excerpt for each sitemap entry:
											<code>[simple-sitemap excerpt='true|false']</code></li>
										<li>HTML tag to wrap the excerpt (if displayed):
											<code>[simple-sitemap excerpt_tag='span']</code></li>
										<li>Comma separated list of post ID's to exclude from the sitemap:
											<code>[simple-sitemap exclude='1,2,3,4']</code></li>
										<li>Show the label for each post type:
											<code>[simple-sitemap show_label='true|false']</code></li>
										<li>Display sitemap items as links or plain text:
											<code>[simple-sitemap links='true|false']</code></li>
										<li>Display the post featured image (if defined) next to each sitemap item as an icon:
											<code>[simple-sitemap image='true|false']</code></li>
										<li>Display the sitemap items with or without bullet points:
											<code>[simple-sitemap list_icon='true|false']</code></li>
										<li>Show a separator between sitemap items:
											<code>[simple-sitemap separator='true|false']</code></li>
										<li>If multiple tabbed sitemaps are to be displayed then this attribute is useful to avoid CSS id conflicts:
											<code>[simple-sitemap id='999']</code></li>
										<li>The sitemap can now also be show as a continuous horizontal list (separate list for each post type):
											<code>[simple-sitemap horizontal='true']</code><br><b>Note: For this to work the <code>render</code> attribute needs to be empty (which it is by default)</b>.</li>
										<li>You can customize the horizontal list separator too:
											<code>[simple-sitemap horizontal='true' horizontal_separator=' | ']</code><br><b>Note: For this to work the <code>horizontal</code> attribute needs to be set to true</b>.</li>
										<li>Optionally set each sitemap link to 'nofollow': <code>[simple-sitemap nofollow="1"]</code></li>
										<li>Display only public posts/pages: <code>[simple-sitemap visibility="public"]</code></li>
									</ol>

									Here's a full list of attributes for the [simple-sitemap] shortcode with default values:
									<ul>
										<li><code>render=''</code></li>
										<li><code>types='post, page'</code></li>
										<li><code>page_depth=0</code></li>
										<li><code>title_tag=''</code></li>
										<li><code>post_type_tag='h3'</code></li>
										<li><code>orderby='title'</code></li>
										<li><code>order='asc'</code></li>
										<li><code>excerpt='false'</code></li>
										<li><code>excerpt_tag='span'</code></li>
										<li><code>exclude=''</code></li>
										<li><code>show_label='true'</code></li>
										<li><code>links='true'</code></li>
										<li><code>image='false'</code></li>
										<li><code>list_icon='true'</code></li>
										<li><code>separator='false'</code></li>
										<li><code>id='1'</code></li>
										<li><code>horizontal='false'</code></li>
										<li><code>horizontal_separator=', '</code> (note the space, if required in outputted horizontal list)</li>
										<li><code>nofollow='0'</code></li>
										<li><code>visibility='all'</code></li>
									</ul>
								</div>

								<div style="background:#fff;border: 1px dashed #ccc;padding: 15px;padding-top:5px;margin-top:15px;">
									<h2><?php _e( '[foody-simple-sitemap-group] shortcode', 'simple-sitemap' ); ?></h2>

									<p>Use this shortcode to display a sitemap for a single post type showing posts grouped by taxonomy terms. Note: Using this shortcode with no attributes outputs a list of posts grouped by taxonomy: <code>[foody-simple-sitemap-group]</code></p>

									<p>There are many attributes you can use to customize the look of your sitemap. Attributes can be combined as required for an even more flexible sitemap layout.</p>

									<ol style="line-height:22px;">
										<li>Select post type:
											<code>[foody-simple-sitemap-group type='post']</code>
											<p style="margin-top:8px;margin-bottom:12px;"><?php printf( __( 'Choose from any of the following registered post types currently available:', 'simple-sitemap' ) ); ?>
												<?php
												$post_type_args = array(
													'public'   => true
												);
												$registered_post_types = get_post_types($post_type_args);
												$registered_post_types_str = implode(', ', $registered_post_types);
												echo '<code>' . $registered_post_types_str . '</code>';
												?></p>
										</li>
										<li>Select post taxonomy:
											<code>[foody-simple-sitemap-group tax='category']</code></li>
										<li>Wrap each sitemap entry in a specific HTML tag:
											<code>[foody-simple-sitemap-group title_tag='h3']</code></li>
										<li>Optionally show a post excerpt for each sitemap entry:
											<code>[foody-simple-sitemap-group excerpt='true|false']</code></li>
										<li>HTML tag to wrap the excerpt (if displayed):
											<code>[foody-simple-sitemap-group excerpt_tag='span']</code></li>
										<li>Display sitemap items as links or plain text:
											<code>[foody-simple-sitemap-group links='true|false']</code></li>
										<li>Show a separator between sitemap items:
											<code>[foody-simple-sitemap-group separator='true|false']</code></li>
										<li>Display the post featured image (if defined) next to each sitemap item as an icon:
											<code>[foody-simple-sitemap-group image='true|false']</code></li>
										<li>Order posts for each post type via title, date, author, ID, menu_order etc. See full list of options <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>: <code>[foody-simple-sitemap-group orderby='title']</code></li>
										<li>Whatever the 'orderby' attribute is set to the 'order' attribute will sort them in either ascending or descending:
											<code>[foody-simple-sitemap-group order='asc|desc']</code></li>
										<li>Display the sitemap items with or without bullet points:
											<code>[foody-simple-sitemap-group list_icon='true|false']</code></li>
										<li>Order post taxonomy term labels by name, date etc.
											<code>[foody-simple-sitemap-group term_orderby='title']</code></li>
										<li>Whatever the 'term_orderby' attribute is set to the 'term_order' attribute will sort them in either ascending or descending:
											<code>[foody-simple-sitemap-group term_order='asc|desc']</code></li>
										<li>Comma separated list of taxonomy terms (e.g. categories) to exclude from the sitemap:
											<code>[foody-simple-sitemap-group exclude_terms="term1, term2"]</code></li>
										<li>Comma separated list of taxonomy terms (e.g. categories) to include in the sitemap:
											<code>[foody-simple-sitemap-group include_terms="term1, term2"]</code></li>
										<li>Comma separated list of post ID's to exclude from the sitemap:
											<code>[foody-simple-sitemap-group exclude='1,2,3,4']</code></li>
										<li>Wrap the post type label in a specific HTML tag:
											<code>[foody-simple-sitemap-group post_type_tag='h2']</code></li>
										<li>Show the label for the post type:
											<code>[foody-simple-sitemap-group show_label='true|false']</code></li>
										<li>Optionally set each sitemap link to 'nofollow': <code>[foody-simple-sitemap-group nofollow="1"]</code></li>
										<li>Display only public posts/pages: <code>[simple-sitemap visibility="public"]</code></li>
									</ol>

									Here's a full list of attributes for the [foody-simple-sitemap-group] shortcode with default values:
									<ul>
										<li><code>type='post'</code></li>
										<li><code>tax='category'</code></li>
										<li><code>title_tag=''</code></li>
										<li><code>excerpt='false'</code></li>
										<li><code>excerpt_tag='div'</code></li>
										<li><code>links='true'</code></li>
										<li><code>separator='false'</code></li>
										<li><code>image='false'</code></li>
										<li><code>orderby='title'</code></li>
										<li><code>order='asc'</code></li>
										<li><code>list_icon='true'</code></li>
										<li><code>term_orderby='name'</code></li>
										<li><code>term_order='asc'</code></li>
										<li><code>exclude=''</code></li>
										<li><code>exclude_terms=''</code></li>
										<li><code>post_type_tag='h3'</code></li>
										<li><code>show_label='true'</code></li>
										<li><code>nofollow='0'</code></li>
										<li><code>visibility='all'</code></li>
									</ul>
								</div>

								<div style="background:#fff;border: 1px dashed #ccc;padding: 15px;padding-top:5px;margin-top:15px;">
									<h2><?php _e( '[simple-sitemap-child] shortcode', 'simple-sitemap' ); ?></h2>

									<p>Use this shortcode to display all the child pages of a specific parent page. <code>[simple-sitemap-child]</code></p>

									<p>This shortcode is basically a wrapper for the <a href="https://developer.wordpress.org/reference/functions/wp_list_pages/" target="_blank"><code>wp_list_pages()</code></a> WordPress function. So, you can use any parameter of that function as a shortcode attribute to control sitemap output.</p>

									<p>You can optionally output the parent page too via the <code>title_li</code> attribute. By default, if you don't add this attribute, the parent parent page link will be included in the list of pages. You can also specify a custom label (which won't be a link).</p>

									<br>
									<p><code>[simple-sitemap-child]</code> or <code>[simple-sitemap-child title_li=""]</code> (parent page will NOT be included in the list of pages)</p>

									<br>
									<p><code>[simple-sitemap-child title_li="@"]</code> (includes the parent page link in the list of pages)</p>

									<br>
									<p><code>[simple-sitemap-child title_li="Parent Page"]</code> (will show the text "Parent Page" as the parent label)</p>

									<br>
									<p>Optionally set each sitemap link to 'nofollow': <code>[simple-sitemap-child nofollow="1"]</code></p>

									<br>
									<p>If you want to show the parent page label <strong>but not as part of the list</strong> then leave out the <code>title_li</code> attribute, and manually add the label outside of the shortcode.</p>
								</div>

								<div style="background:#fff;border: 1px dashed #ccc;padding: 15px;padding-top:5px;margin-top:15px;">
									<h2><?php _e( '[foody-simple-sitemap-tax] shortcode', 'simple-sitemap' ); ?></h2>

									<p>Use this shortcode to display a list of post taxonomies (e.g. categories). Any publicly registered taxonomy can be listed using this shortcode. Note: Using this shortcode with no attributes outputs a list of post categories.</p>

									<p>There are many attributes you can use with this shortcode. Attributes can be combined as required for an even more flexible sitemap layout.</p>

									<p>Here's the full list of attributes for the <code>[foody-simple-sitemap-tax]</code> shortcode with default values:</p>
									<ul>
										<li><code>include=''</code></li>
										<li><code>exclude=''</code></li>
										<li><code>depth='0'</code></li>
										<li><code>child_of='0'</code></li>
										<li><code>title_li=''</code></li>
										<li><code>show_count='false'</code></li>
										<li><code>orderby='name'</code></li>
										<li><code>order='ASC'</code></li>
										<li><code>taxonomy='category'</code></li>
										<li><code>hide_empty='0'</code></li>
									</ul>
									<p>As this shortcode implements the <code>wp_list_categories()</code> function internally, the attributes match the function arguments closely. Please see the <a href="https://developer.wordpress.org/reference/functions/wp_list_categories/" target="_blank">function documentation</a> for more information about each attribute.</p>
								</div>

								<div style="background:#fff;border: 1px dashed #ccc;padding: 15px;padding-top:5px;margin-top:15px;">
									<h2><?php _e( '[simple-sitemap-menu] shortcode', 'simple-sitemap' ); ?></h2>

									<p>Use this shortcode to display a sitemap based on a nav menu.</p>

									<p>Here's a list of attributes for the <code>[simple-sitemap-menu]</code> shortcode with default values:</p>
									<ul>
										<li><code>menu='[name of menu, or menu ID, slug, object]'</code> e.g. <code>menu='Main Menu'</code></li>
									</ul>
									<p>This shortcode implements the <code>wp_nav_menu()</code> function internally, and the attributes match the function arguments closely. See the <a href="https://developer.wordpress.org/reference/functions/wp_nav_menu/" target="_blank">function documentation</a> for more information about each attribute.</p>
								</div>
							</td>
						</tr>

						<tr valign="top" style="display:none;">
							<th scope="row">Advanced Configuration</th>
							<td>
								<textarea name="<?php echo $this->_args['options_db_name']; ?>[txtar_sitemap_script]" rows="7" cols="50" type='textarea'><?php echo $options['txtar_sitemap_script']; ?></textarea>
								<p class="description">Add script into the box above to output an advanced sitemap.</p>
							</td>
						</tr>

					</table>
				</div>

				<div class="support-tab tab-content">
					<?php do_settings_sections( $this->_args['menu_slug'] ); ?>
				</div>

				<?php submit_button(); ?>

			</form>
			<!-- main form closing tag -->

			<form action="<?php echo self::currURL(); // current page url ?>" method="post" id="simple-sitemap-pro-reset-form" style="display:inline;">
				<span id="simple-sitemap-pro-reset">
					<a href="#">Reset plugin options</a>
					<input type="hidden" name="reset_options" value="true">
				</span>
			</form>
		</div><!-- .wrap -->
	<?php
	}

	/**
	 * Register admin scripts and styles to be enqueued on the plugin options page
	 *
	 * @since 0.1.0
	 */
	public function register_admin_scripts() {

		// Register plugin style sheets
		wp_register_style( 'wpgo-simple-sitemap-pro-admin-css', plugins_url( 'css/simple-sitemap-pro-admin.css', $this->_args['plugin_root'] ) );

		// Register plugin scripts
		wp_register_script( 'wpgo-simple-sitemap-pro-admin-js', plugins_url( 'js/simple-sitemap-pro-admin.js', $this->_args['plugin_root'] ) );
	}

	/**
	 * Enqueue scripts for options page.
	 *
	 * @since 0.1.0
	 */
	public function plugin_admin_scripts() {

		/* Scripts for plugin options page only. */
		wp_enqueue_script( 'wpgo-simple-sitemap-pro-admin-js' );

		// localize variables to be accessible in simple-sitemap-pro-admin.js
		wp_localize_script(
			'wpgo-simple-sitemap-pro-admin-js',
			'cf_vars',
			array(
				'cf_nonce' => wp_create_nonce( 'cf-nonce' )
			)
		);
	}

	/**
	 * Enqueue scripts for the front end
	 *
	 * @since 0.1.0
	 */
	public function register_front_end_scripts() {

		wp_register_style( 'wpgo-simple-sitemap-pro-css', plugins_url( 'css/simple-sitemap-pro.css', $this->_args['plugin_root'] ) );
		wp_register_style( 'wpgo-simple-sitemap-pro-jquery-ui-css', plugins_url( 'css/simple-sitemap-pro-jquery-ui.css', $this->_args['plugin_root'] ) );
		wp_register_script( 'wpgo-simple-sitemap-pro-js', plugins_url( 'js/simple-sitemap-pro.js', $this->_args['plugin_root'] ), array( 'jquery-ui-tabs' ) );
	}

	/**
	 * Enqueue styles for plugin options page.
	 *
	 * @since 0.1.0
	 */
	public function plugin_admin_styles() {

		/* Styles for plugin options page only. */
		wp_enqueue_style( 'wpgo-simple-sitemap-pro-admin-css' );
	}

	/**
	 * Display a 'Settings' link to the Plugins options page from the main Plugins archive page.
	 *
	 * @since 0.1.0
	 */
	public function plugin_settings_link( $links, $file ) {

		if ( $file == plugin_basename( $this->_args['plugin_root'] ) ) {
			$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=' . $this->_args['menu_slug'] . '">' . __( 'Settings', 'simple-sitemap-pro-plugin' ) . '</a>';
			// make the 'Settings' link appear first
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Get URL of current page.
	 *
	 * @since 0.1.0
	 */
	public static function currURL() {
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) ) {
			if ( $_SERVER["HTTPS"] == "on" ) {
				$pageURL .= "s";
			}
		}
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}

		return $pageURL;
	}
}