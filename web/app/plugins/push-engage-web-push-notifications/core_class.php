<?php

    class Pushengage {
        private static $pushengage = null;
        public static $pushengage_version = '3.2.0';
        public static $database_version = '07-06-2021';
        public function __construct() {
            global $pe_session;
        }

        /*
         * entry point or initialization function.
         */
        public static function init() {
            if(is_null( self::$pushengage)) {
                self::$pushengage = new self();
                $pushengage_settings = self::pushengage_settings();
                if(empty( $pushengage_settings) || self::$pushengage_version !== $pushengage_settings['version'] ) {
                    self::install($pushengage_settings);
                }

                self::add_actions();
            }

        }

        /*
         * check for pushengage api_key.
         * check for admin pages only.
         */
        public static function checkUserAuthenticaiton($api_key) {
            if(!empty($api_key) && $_GET['page'] == 'pushengage-admin' && empty($pe_session['check_auth'])) {
                $pe_session['check_auth'] = Api_class::verifyUser($api_key);
                return $pe_session['check_auth'];
            }
        }

        // insert pushengage inline code in the source code.
        public static function print_pe_inline_script() {
            $script =  "window._peq = window._peq || []; window._peq.push(['init']);";
            wp_add_inline_script('pushengage-core', $script, 'before');
        }

        /*
         * actions to be performed to after insallation & activation of plugin.
         */
        public static function add_actions() {
            $pushengage_settings = self::pushengage_settings();

            if (!empty($pushengage_settings)) {
                add_action('wp_enqueue_scripts', array( __CLASS__, 'print_pe_clientcdn_script'));

                // register action hook to send notification on post get published.
                add_action('transition_post_status', array( __CLASS__, 'send_pe_push_notifications' ), 10, 3);

                // if subscription popup is not blocked then only we add async to core script
                if($pushengage_settings['disable_subscription_popup'] != true) {
                    add_action( 'wp_enqueue_scripts', array( __CLASS__, 'print_pe_inline_script') );
                }

                // include subscripiton popup core script with async mode
                add_filter('script_loader_tag', function ($tag, $handle) {
                    if('pushengage-core' !== $handle) {
                        return $tag;
                    }

                    return str_replace( ' src', ' async src', $tag );
                }, 10, 2 );

                // wp-rocket filter hook to whitelist pushengage clientcdn script.
                add_filter('rocket_minify_excluded_external_js', function ($excluded_external_js) {
                    if(gettype($excluded_external_js) == 'array') {
                        $excluded_external_js[] = 'pushengage';
                    }

                    return $excluded_external_js;
                });

                // wp-rocket filter hook to whitelist inline js code.
                add_filter('rocket_excluded_inline_js_content', function ($excluded_inline_js) {
                    if(gettype($excluded_inline_js) == 'array') {
                        $excluded_inline_js[] = 'window._peq';
                    }
                
                    return $excluded_inline_js;
                });


                /**
                 * Injects the segment addition scripts in the frontend for the category based segmentation, when scripts are enqueued.
                 * 
                 * @since 3.2.0
                 * 
                 */
                add_action('wp_enqueue_scripts', array( __CLASS__, 'print_pe_segment_addition_script'));

            }

            // admin level actions.
            if (is_admin()) {
                add_action( 'init', array( __CLASS__, 'init_user_options' ) );
            }

        }

        /*
         * getting pushengage option values from wordpress local database.
         */
        public static function pushengage_settings() {
            return get_option( 'pushengage_settings' );
        }

        /*
         * check wehere pushengage plugin is active ot not.
         */
        public static function is_pushengage_active() {
            $pushengage_settings = self::pushengage_settings();
            $api_key = $pushengage_settings['appKey'];
            if (!empty($api_key)){
                return true;
            } else {
                return false;
            }
        }

        /*
         * adding an option to override the already saved setting to send push notification.
         * this feature is present in the right side of the screen in the post or page publishing page.
         */
        public static function add_pe_notification_override_meta_box() {
            $pushengage_settings = self::pushengage_settings();
            if(!empty($pushengage_settings['all_post_types'])) {
                $screens = get_post_types();
            } else {
                $screens = array('post');
            }

            foreach ($screens as $screen) {
                add_meta_box(
                    'pushengage_notif_on_post',
                    'PushEngage Push Notification',
                    array( __CLASS__, 'notification_override' ),
                    $screen,
                    'side',
                    'high'
                );
            }
        }


        // functionality of pushengage notification_override_meta_box.
        public static function notification_override() {

            if(empty($pe_session['pushengage_settings'])) {
                $pushengage_settings = self::pushengage_settings();
                $pe_session['pushengage_settings'] = $pushengage_settings;

            } else {
               $pushengage_settings = $pe_session['pushengage_settings'];
            }

            $api_key = $pushengage_settings['appKey'];
            $auto_push = $pushengage_settings['autoPush'];
            $all_post_types = $pushengage_settings['all_post_types'];
            global $post;
            $check_auth = !empty($pe_session['check_auth'])?$pe_session['check_auth']:'';

            if(!empty($check_auth['block_user'])) {
                return false;
            }

            if('post' === $post->post_type || true === $all_post_types) {
                printf('<div style="padding-left:10px;padding-bottom:15px" id="pushengage-post-checkboxes">');
                $display_segments_div = " display:none; ";

                // "Send PushEngage Notification" or "Send PushEngage Notification" in the notification overrride meta box.
                if('auto-draft' === $post->post_status ) {
                    // send notification check box is "checked", if auto_push is true otherwise we should uncheck this.
                    if(true === $auto_push) {
                        printf( '<label><input type="checkbox" value="1" checked id="pushengage-override-checkbox" name="pushengage-override" style="margin: -3px 9px 0 1px;" checked onclick="selectPeNotifcationsOption()"/>');
                        echo 'Send PushEngage Notification</label>';
                        $display_segments_div = "style='display:block'";

                    } else {
                        printf( '<label><input type="checkbox" value="1" id="pushengage-override-checkbox" name="pushengage-override" style="margin: -3px 9px 0 1px;" onclick="selectPeNotifcationsOption()"/>');
                        echo 'Send PushEngage Notification</label>';
                    }

                } else {
                    // check for override and scheduled options.
                    $pe_override = get_post_meta( $post->ID, '_pe_override', true );
                    $pe_scheduled = get_post_meta( $post->ID, 'pe_override_scheduled', true );

                    if(($pe_override == '1' || $pe_scheduled == '1') && $post->post_status!='publish') {
                        $upd_chk_box = 'checked';
                        $display_segments_div = " display:block; ";
                    } else {
                        $upd_chk_box = '';
                    }

                    printf('<label><input type="checkbox" value="1" id="pushengage-override-checkbox" name="pushengage-override" style="margin: -3px 9px 0 1px;" '.$upd_chk_box.'  onclick="selectPeNotifcationsOption()"/>');
                    echo 'Send PushEngage Notification on Update</label>';
                }

                // check boxes in override meta box, used to indicate send to all or the segmented subscribers.
                $draft_segments = get_post_meta( $post->ID, '_pe_draft_segments', true );
                if(!empty($draft_segments)) {
                    $draft_segments = explode(' ', $draft_segments);
                }

                echo "<script>
                    function selectPeNotifcationsOption() {

                        if(document.getElementById('pushengage-override-checkbox').checked == true) {
                            var drft_seg ='".$draft_segments."';
                            if(!drft_seg && document.getElementById('select-all-pe-Subscribers')) {
                                document.getElementById('select-all-pe-Subscribers').checked=true;
                            }
                            if(document.getElementById('pe-segmented-post')) {
                                document.getElementById('pe-segmented-post').style.display = 'block';
                            }

                        } else {
                            if(document.getElementById('select-all-pe-Subscribers')) {
                                document.getElementById('select-all-pe-Subscribers').checked=false;
                            }
                            if(document.getElementById('pe-segmented-post')) {
                                document.getElementById('pe-segmented-post').style.display = 'none';
                            }

                        }
                        }
                    </script>";

                echo '</div>';

                if(empty($pe_session['segmets_data'])) {
                    $segmets_data = Api_class::getSegments($api_key);

                } else {
                    $segmets_data = $pe_session['segmets_data'];
                }

                if(!empty($segmets_data["segments"])) {
                    $pe_override = get_post_meta( $post->ID, '_pe_override', true );

                    if($auto_push || $all_post_types || empty($draft_segments)) {
                        $check = "checked";

                    } else {
                        $check = "";
                    }

                    if($post->post_status == 'auto-draft' && ($auto_push || $all_post_types)) {
                        $check ='checked';
                    }

                    printf('<div style="padding-left:37px;padding-top:0px;padding-bottom:10px;'.$display_segments_div.'" id="pe-segmented-post"><span style="font-weight:bold;">Select PushEngage Segments</span>');
                    echo '<br><input type="checkbox" id="select-all-pe-Subscribers" '. $check.' onclick="selectAllPeSubscribers();"><span  style="margin-left:10px;">All Subscribers</span>';
                    foreach($segmets_data["segments"] as $segment) {
                        if(!empty($draft_segments) && in_array($segment["segment_id"], $draft_segments)) {
                            $seg_chk_box = 'checked';
                        } else {
                            $seg_chk_box = '';
                        }

                        if(!empty($segment["segment_name"])) {
                            echo '<div style="margin:5px 10px 5px 0px !important;"><input type="checkbox"   '.$seg_chk_box.'class="pushengage-segments" onclick="selectPeSegment()" name="pushengage-categories[]" value="'.$segment["segment_id"].'" ><span style="margin-left:10px;">'.$segment["segment_name"].'</span></div>';
                        }
                    }

                    echo '</div>';
                    echo '<script>
                        function selectAllPeSubscribers() {
                            var all_pe_subscribers = document.getElementById("select-all-pe-Subscribers").checked;
                            var pe_segments = document.getElementsByClassName("pushengage-segments");

                            for (var key in pe_segments) {
                                if (pe_segments.hasOwnProperty(key)) {
                                    if(all_pe_subscribers) {
                                        pe_segments[key].checked = false;
                                    } else {
                                        pe_segments[key].checked = true;
                                    }
                                }
                            }
                        }

                        function selectPeSegment() {
                            var pe_segments = document.getElementsByClassName("pushengage-segments");
                            var check_flag = false;

                            for (var key in pe_segments) {
                                if(pe_segments[key].checked == true) {
                                    check_flag = true;
                                }
                            }

                            if(check_flag==false) {
                                document.getElementById("select-all-pe-Subscribers").checked = true;
                            } else {
                                document.getElementById("select-all-pe-Subscribers").checked = false;
                            }
                        }
                    </script>';
                }

            }
        }


        /*
         * adding an option to customize the push notification message at the time of publishing post.
         * this feature is present in the bottom side of the screen in the post or page publishing page.
         */
        public static function add_pe_custom_notification_message_field($post_type, $post) {
            $pushengage_settings = self::pushengage_settings();
            $all_post_types = $pushengage_settings['all_post_types'];

            if('post' === $post_type || true === $all_post_types) {
                if ('attachment' !== $post_type && 'comment' !== $post_type && 'dashboard' !== $post_type && 'link' !== $post_type) {
                    add_meta_box (
                        'pushengage_meta',
                        'Custom Notification Title',
                        array( __CLASS__, 'custom_notification_message_field' ),
                        '',
                        'normal',
                        'high'
                    );
                }
            }
        }

        // funtionality of pushengage custom_notification_message_field.
        public static function custom_notification_message_field($post) {
            $custom_note_text = get_post_meta( $post->ID, '_pushengage_custom_text', true );
            ?>
            <div id="pushengage-custom-note" class="form-field form-required">
                <input type="text" id="pushengage-custom-note-text" maxlength="73" placeholder="Enter Custom Headline For Your Notification" name="pushengage-custom-msg" value="<?php echo ! empty( $custom_note_text ) ? esc_attr( $custom_note_text ) : ''; ?>" /><br>
                <span id="pushengage-custom-note-text-description" >Custom headline limit 73 characters.<br/>When using a custom headline, this text will be used in place of the default blog post title for your push notification.</span>
            </div>
            <?php
        }


        /**
         * Storing the user selected settings from the pushengage admin screen to the wordpress local database.
         * 
         * @since 1.0.0
         */
        public static function pushengage_save_settings() {

            if ( isset( $_POST['action'] ) && 'update_wordpress_settings' === $_POST['action'] && 'pushengage-admin' === $_GET['page'] ) {

                $tab                            = 'gSettings';
                $pushengage_settings            = self::pushengage_settings();
                $api_key                        = $pushengage_settings['appKey'];
                $auto_push                      = $pushengage_settings['autoPush'];
                $use_featured_image             = $pushengage_settings['use_featured_image'];
                $all_post_types                 = $pushengage_settings['all_post_types'];
                $utmcheckbox                    = $pushengage_settings['utmcheckbox'];
                $utm_source                     = $pushengage_settings['utm_source'];
                $utm_medium                     = $pushengage_settings['utm_medium'];
                $utm_campaign                   = $pushengage_settings['utm_campaign'];
                $disable_subscription_popup     = $pushengage_settings['disable_subscription_popup'];
                $category_segmentation          = !empty( $pushengage_settings['category_segmentation'] ) ? $pushengage_settings['category_segmentation'] : '' ;

                // customize wordpress notification push settings from pushengage admin screen like auto-push, all-types-of-post etc
                if ( isset( $_POST['action_settings'] ) && 'post' === $_POST['action_settings'] ) {
                    $auto_push                  = isset( $_POST['pushengage-auto-push'] ) ? true : false;
                    $all_post_types             = isset( $_POST['pushengage-all-post-types'] ) ? true : false;
                    $disable_subscription_popup = isset( $_POST['disable_subscription_popup'] ) ? true: false;

                    if ( isset( $_POST['pushengage-custom-image'] ) && 1 === $_POST['pushengage-custom-image'] ) {
                        $use_featured_image = true;
                    } else {
                        $use_featured_image = false;
                    }
                }

                // customize wordpress notification push UTM settings from pushengage admin screen.
                if ( isset( $_POST['action_settings'] ) && 'utm' === $_POST['action_settings'] ) {

                    if( isset( $_POST['utmcheckbox'] ) ) {
                        $utmcheckbox = true;
                        if ( isset( $_POST['utm_source'] ) ) {
                            $utm_source = sanitize_text_field( $_POST['utm_source'] );
                        }

                        if( isset( $_POST['utm_medium'] ) ) {
                            $utm_medium = sanitize_text_field( $_POST['utm_medium'] );
                        }

                        if( isset( $_POST['utm_campaign'] ) ) {
                            $utm_campaign = sanitize_text_field( $_POST['utm_campaign'] );
                        }

                    } else {
                        $utmcheckbox    = false;
                        $utm_source     = !empty( $_POST['utm_source'] ) ? sanitize_text_field( $_POST['utm_source'] ) : '';
                        $utm_medium     = !empty( $_POST['utm_medium'] ) ? sanitize_text_field( $_POST['utm_medium'] ) : '';
                        $utm_campaign   = !empty( $_POST['utm_campaign'] ) ? sanitize_text_field( $_POST['utm_campaign'] ) : '';
                    }
                }

                /* 
                 *  customize/update category segmentation settings from pushengage admin screen.
                 *  Object format is :- 
                 *  category_segmentation: {
                 *      enabled: 0 | 1,
                 *      settings: [
                 *           {
                 *               "category_name": string,
                 *               "segment_name": string,
                 *               "status": 0 | 1
                 *           }
                 *       ]
                 *   }
                 *
                */
                if ( isset( $_POST['action_settings'] ) && 'update_category_segmentation_settings' === $_POST['action_settings'] ) {
                    $settings = array();
                    $enabled  = false;
                    $pe_segment_list = array();

                    if( "1" === $_POST['category_segmentation_enabled'] ) {
                        $enabled = true;
                    }

                    // get segment ids from the segment api.
                    $segmets_data = Api_class::getSegments( $api_key  );

                    if( !empty( $segmets_data['segments'] ) && 0 < count( $segmets_data['segments'] ) ) {
                        $pe_segment_list = $segmets_data['segments'];
                    }

                    for ( $i = 0; $i < count( $_POST ); $i++ ) {

                        if( !empty( $_POST['category_name_'.$i] ) && !empty( $_POST['segment_name_'.$i] ) && "Select a segment" !== $_POST['segment_name_'.$i] ) {
                            $category_name      = sanitize_text_field( $_POST['category_name_'.$i] );
                            $segment_name       = sanitize_text_field( $_POST['segment_name_'.$i] );
                            $segment_checkbox   = 0;

                            //getting segment id by segment name
                            foreach($pe_segment_list as $segment) {
                                if( $segment['segment_name'] === $segment_name ) {
                                    $segment_id = $segment['segment_id'];
                                }
                            }

                            if(empty($segment_id)) {
                                continue;
                            }

                            if( isset( $_POST['segment_checkbox_'.$i] ) ) {
                                $segment_checkbox = 1;
                            } 

                            $category_segment_setting = array(
                                "category_name" => $category_name,
                                "segment_name"  => $segment_name,
                                "segment_id"    => $segment_id,
                                "status"        => $segment_checkbox
                            );

                            array_push( $settings, $category_segment_setting );
                        }

                    }

                    $category_segmentation_payload = array(
                        "enabled"   => $enabled,
                        "settings"  => $settings
                    );
                    
                    $category_segmentation = json_encode( $category_segmentation_payload );

                    $tab = 'category-segmentation';
                }

                // format data to save in the wordpress local database.
                $form_data = array(
                    'appKey'                        => $api_key,
                    'autoPush'                      => $auto_push,
                    'use_featured_image'            => $use_featured_image,
                    'utmcheckbox'                   => $utmcheckbox,
                    'utm_source'                    => $utm_source,
                    'utm_medium'                    => $utm_medium,
                    'utm_campaign'                  => $utm_campaign,
                    'all_post_types'                => $all_post_types,
                    'disable_subscription_popup'    => $disable_subscription_popup,
                    'category_segmentation'         => $category_segmentation,
                );

                self::update_settings( $form_data );

                // get site site data for the  general setting screen to make sure plugin is acivated.
                $appdata                = self::getSiteData( $api_key );
                $appdata                = $appdata[0];
                $pe_session['appdata']  = $appdata;

                wp_redirect( esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab='.$tab ).'&status=success' ) );
                exit;
            }
        }

        /**
         * Install the plugin by adding the default option values.
         * 
         * @param array $pushengage_settings Already saved pushengage settings.
         * 
         * @since 1.0.0
         */
        public static function install( $pushengage_settings ) {

            if( empty($pushengage_settings ) ) {
                $pushengage_settings = array(
                    'appKey'                        => '',
                    'version'                       => self::$pushengage_version,
                    'autoPush'                      => true,
                    'database_version'              => self::$database_version,
                    'use_featured_image'            => true,
                    'all_post_types'                => true,
                    'utmcheckbox'                   => true,
                    'utm_source'                    => 'pushengage',
                    'utm_medium'                    => 'pushengage',
                    'utm_campaign'                  => 'pushengage',
                    'disable_subscription_popup'    => false,
                    'category_segmentation'         => '',
                );

                add_option( 'pushengage_settings', $pushengage_settings );
            }

            if ( !empty( $pushengage_settings['version']) && self::$pushengage_version !== $pushengage_settings['version'] ) {
                self::update( $pushengage_settings );
            }
        }

        /*
         *  update pushengage plugin to newer version.
         */
        public static function update($pushengage_settings) {
            $pushengage_settings['version'] = self::$pushengage_version;
            if(empty($pushengage_settings['site_name'])) {
                if(!empty($pe_session['appdata']['site_name'])) {
                    $pushengage_settings['site_name'] = $pe_session['appdata']['site_name'];

                } else {
                    if(!empty($_POST['api_key'])) {
                        $appdata = self::getSiteData(sanitize_text_field($_POST['api_key']));
                        $appdata = $appdata[0];

                        if(isset($appdata['site_name']) && !empty($appdata['site_name'])) {
                            $pushengage_settings['site_name'] = $appdata['site_name'];
                        }
                    }
                }

            }
            update_option( 'pushengage_settings', $pushengage_settings );
        }

        /*
         *  adding the pushengage wordpress dashboard screens.
         */
        public static function add_pe_admin_menu() {
            add_menu_page (
                'Pushengage',
                'PushEngage',
                'manage_options',
                'pushengage-admin',
                array( __CLASS__, 'pe_admin_menu_page' ),
                PUSHENGAGE_URL . 'images/pe_logo.png'
            );
        }

        // adding pushengage admin screen pages.
        public static function pe_admin_menu_page() {
            $pushengage_settings = self::pushengage_settings();
            $api_key = $pushengage_settings['appKey'];
            $cat_args = array(
                'hide_empty' => 0,
                'order' => 'ASC'
            );

            if(empty($api_key)) {
                $pe_session['menu_active_key'] = false;
                // initially api verification error is false.
                $is_api_key_verification_error = false;
            }

            /*
             * activating the pushengage plugin by adding API key.
             */
            if(isset($_POST['api_key']) && $_POST['api_key'] && $_POST['action'] == 'update_api_key') {
                $tab_start = "setup";
                $appdata = self::getSiteData(sanitize_text_field($_POST['api_key']));

                // logging error, if server gives any error while getting site data.
                if(isset($appdata['success']) && $appdata['success'] == false) {
                    $pe_session['menu_active_key'] = false;
                    $is_api_key_verification_error = true;
                    // error_code and error_message to show, in case of activating plugin.
                    $pe_session['error_code'] =  $appdata['error_code'];
                    $pe_session['message'] = $appdata['message'];

                    // "$debug_info" array data for logging purpose.
                    $debug_info['method'] = 'getSiteData';
                    $debug_info['message'] = $appdata['message'];
                    $debug_info['api_key'] = $_POST['api_key'];
                    Api_class::sendCodeErrorReport($debug_info);

                } else {

                    $appdata = $appdata[0];
                    $pe_session['appdata'] = $appdata;

                    if(!empty($appdata)) {
                        $pe_session['menu_active_key'] = true;
                        $pushengage_settings['appKey'] = sanitize_text_field($_POST['api_key']);

                        if($appdata['site_id']) {
                            $pushengage_settings['site_id'] = $appdata['site_id'];
                        }

                        if($appdata['site_name']) {
                            $pushengage_settings['site_name'] = $appdata['site_name'];
                        }

                        if($appdata['site_key']) {
                            $pushengage_settings['site_key'] = $appdata['site_key'];
                        }

                        $service_worker_path=PUSHENGAGE_PLUGIN_URL."packages/service-worker.js.php?domain=".$appdata['site_subdomain'];

                        $service_worker_data = array (
                            'type' => 'update_service_worker_setting',
                            'worker' => $service_worker_path,
                            'workerStatus' => true,
                            'scope' => true,
                        );

                        Api_class::updateServiceWorkerSetting($_POST['api_key'], $service_worker_data);

                        self::update($pushengage_settings);
                        $pushengage_settings = self::pushengage_settings();
                        $api_key = $pushengage_settings['appKey'];

                    } else {
                        $pe_session['menu_active_key'] = false;
                        $is_api_key_verification_error = true;
                    }
                }

            }


            /*
             * show the pushengage admin pages, if and only if api_key is present.
             * Also, handle the all form submissions inside the admin screens.
             */
            if(!empty($api_key)) {
                // check user authentication
                $pe_session['check_auth'] = !empty($pe_session['check_auth']) ? $pe_session['check_auth'] : self::checkUserAuthenticaiton($api_key);
                if(empty($pe_session['check_auth']['error_code'])) {
                    $pe_session['menu_active_key'] = true;

                } else {
                    $pe_session['menu_active_key'] = false;
                    // logging error, if server gives any error while user verification.
                    $debug_info['method'] = 'checkUserAuthenticaiton';
                    $debug_info['message'] = $pe_session['check_auth']['message'];
                    $debug_info['api_key'] = $api_key;
                    Api_class::sendCodeErrorReport($debug_info);
                }

                // site info
                if(empty($pe_session['appdata'])) {
                    $appdata = self::getSiteData($api_key);

                    if(!empty($appdata[0])) {
                        $appdata = $appdata[0];
                        $pe_session['appdata'] = $appdata;
                    }
                }


                /*
                 * Get general settings data for general settings screen.
                 */
                if(empty($_GET['tab']) || ($_GET['tab'] == 'gSettings') && $_GET['page']=='pushengage-admin') {
                    if(empty($pe_session['tabdata'])) {
                        $general_settings = Api_class::getGeneralSettings($api_key);

                        // pe api call getting data/error.
                        if(empty($general_settings) || ( isset($general_settings['success']) && $general_settings['success'] == false ) ||  $general_settings == NULL) {
                            $pe_api_error = !empty($general_settings['message'])?$general_settings['message']:'something went wrong while getting your general settings.';
                        } else {
                            $pe_session['tabdata'] = $general_settings;
                            // sync pushengage dashabord site_name & pushengage plugin local db site_name.
                            if( isset($general_settings['site_info']['site_name']) && $pushengage_settings['site_name'] != $general_settings['site_info']['site_name']){
                                $pushengage_settings['site_name'] = $general_settings['site_info']['site_name'];
                                self::update($pushengage_settings);
                            }
                        }
                    }
                }


                /*
                 * Get subscription popup settings data for subscription dialogbox screen.
                 */
                if(!empty($_GET['tab']) && $_GET['tab'] == 'subDialogbox' && empty($pe_session['optin_settings_data'])) {
                    if(empty($pe_session['tabdata'])) {
                        $optin_settings_data = Api_class::getSubscriptionPoupSettings($api_key);

                        // pe api call getting data/error.
                        if(empty($optin_settings_data) || ( isset($optin_settings_data['success']) && $optin_settings_data['success'] == false ) ||  $optin_settings_data == NULL) {
                            $pe_api_error = !empty($optin_settings_data['message']) ? $optin_settings_data['message'] : 'something went wrong while getting your subscription dialogbox settings.';
                        } else {
                            $pe_session['tabdata'] = $optin_settings_data;
                        }


                        if(!empty($optin_settings_data['site_info']['optin_settings'])) {
                            $optin_settings = json_decode($optin_settings_data['site_info']['optin_settings']);
                            $pe_session['tabdata']['optin_settings_data'] = $optin_settings;
                        }

                        if(!empty($optin_settings_data['segments'])) {
                            $pe_session['tabdata']['segments_data'] = $optin_settings_data['segments'];
                        }

                        if(!empty($optin_settings_data['site_type'])) {
                            $pe_session['tabdata']['site_type'] = $optin_settings_data['site_type'];
                        }
                    }
                }


                /*
                 * Get welcome notification settings data for welcome notification screen.
                 */
                if(!empty($_GET['tab']) && $_GET['tab'] == 'welcome_notification' && empty($pe_session['welcome_note_data'])) {
                    if(empty($pe_session['tabdata'])) {
                        $welcome_note_data = Api_class::getWelcomeNotificationSettings($api_key);

                        // pe api call getting data/error.
                        if(empty($welcome_note_data) || ( isset($welcome_note_data['success']) && $welcome_note_data['success'] == false ) ||  $welcome_note_data == NULL) {
                            $pe_api_error = !empty($welcome_note_data['message']) ? $welcome_note_data['message'] : 'something went wrong while getting your welcome notification settings.';
                        } else {
                            $pe_session['tabdata'] = $welcome_note_data;
                        }

                        if(!empty($welcome_note_data['welcome_notification_info'])) {
                            $pe_session['tabdata']['welcome_note_data'] = json_decode($welcome_note_data['welcome_notification_info']['option_value']);
                        }
                    }

                }


                /*
                 * Get automatic segmentation settings data for automatic segmentation screen.
                 */
                if(!empty($_GET['tab']) && $_GET['tab'] == 'segmentation' && empty($pe_session['segmentation'])) {
                    if(empty($pe_session['tabdata'])) {
                        $automatic_segmentation_data = Api_class::getAutomaticSegmentList($api_key);
                        $get_user_site_plan_info = Api_class::getUserSitePlanInfo($api_key);

                        // pe api call getting data/error.
                        if(empty($automatic_segmentation_data) || ( isset($automatic_segmentation_data['success']) && false === $automatic_segmentation_data['success'] && 'This feature is not available for Free Plan.' !== $automatic_segmentation_data['message'] ) ||  $automatic_segmentation_data == NULL ) {
                            $pe_session['tabdata'] = $get_user_site_plan_info;
                            $pe_api_error = !empty($automatic_segmentation_data['message']) ? $automatic_segmentation_data['message'] : 'something went wrong while getting your automatic segments settings.';

                        } else if(empty($get_user_site_plan_info) || ( isset($get_user_site_plan_info['success']) && $get_user_site_plan_info['success'] == false ) || $get_user_site_plan_info == NULL) {
                            $pe_api_error = !empty($get_user_site_plan_info['message']) ? $get_user_site_plan_info['message'] : 'something went wrong while getting user, site & plan infomation in automatic segmentation settings.';

                        } else {
                            $pe_session['tabdata'] = $get_user_site_plan_info;
                            $pe_session['tabdata']['automatic_segment'] = $automatic_segmentation_data;
                        }

                    }

                }

                /**
                 *Get category segment settings data for category segment settings screen.
                 * 
                 *@since 3.2.0
                 */
                if( empty( $_GET['tab'] ) || ( 'category-segmentation' === $_GET['tab'] ) && 'pushengage-admin' === $_GET['page'] ) {
                    if( empty( $pe_session['tabdata'] ) ) {
                        $segmets_data                   = Api_class::getSegments( $api_key );
                        $user_site_plan_info            = Api_class::getUserSitePlanInfo( $api_key );
                        $pushengage_settings            = Pushengage::pushengage_settings();
                        $category_segmentation          = json_decode( $pushengage_settings['category_segmentation'] );

                        // "$wp_category_list" varibale is an array of wordpress categories.
                        $wp_category_list               = get_categories();

                        // "$pe_segment_list" varibale is an array of pushengage segments.
                        $pe_segment_list                = $segmets_data['segments'];

                        // "$category_segmentation_enabled" variable used to check category segmentation has enabled or not.
                        $category_segmentation_enabled  = false;

                        if( $category_segmentation->enabled ) {
                            $category_segmentation_enabled = true;
                        }

                        // "$wp_category_pe_segment_map" variable is an array of objects contains the segment_name, category_name and status flag mapping.
                        // i.e [ {"segment_name": string, "category_name": string, "status": 1|0} ]
                        $wp_category_pe_segment_map = array();

                        if( !empty( $category_segmentation->settings ) ) {
                            $wp_category_pe_segment_map = $category_segmentation->settings;
                        }

                        // pe api call getting data/error.
                        if( empty( $segmets_data ) || ( ( isset( $segmets_data['success'] ) && false === $segmets_data['success'] && "1007" !== $segmets_data['error_code'] ) ) ||  NULL === $segmets_data ) {
                            $pe_session['tabdata']  = $user_site_plan_info;
                            $pe_api_error           = !empty( $segmets_data['message'] ) ? $segmets_data['message'] : 'something went wrong while getting your automatic segments settings.';

                        } elseif ( empty( $user_site_plan_info ) || ( isset( $user_site_plan_info['success'] ) && false === $user_site_plan_info['success'] ) || NULL === $user_site_plan_info ) {
                            $pe_api_error = !empty( $user_site_plan_info['message'] ) ? $user_site_plan_info['message'] : 'something went wrong while getting user, site & plan infomation in automatic segmentation settings.';

                        } else {
                            $pe_session['tabdata']                                  = $user_site_plan_info;
                            $pe_session['tabdata']['wp_category_list']              = $wp_category_list;
                            $pe_session['tabdata']['pe_segment_list']               = $pe_segment_list;
                            $pe_session['tabdata']['category_segmentation_enabled'] = $category_segmentation_enabled;
                            $pe_session['tabdata']['wp_category_pe_segment_map']    = $wp_category_pe_segment_map;
                        }
                    }
                }

                /*
                 * Update site information from the general settings screen.
                 */
                if (!empty($_POST['action']) && $_POST['action']=="update_site_settings") {
                    $tab = "gSettings";
                    $site_name = sanitize_text_field($_POST['site_name']);
                    $site_url = sanitize_text_field($_POST['site_url']);
                    $site_image = sanitize_text_field($_POST['site_image']);

                    $data = array(
                        'site_id' => $pe_session['appdata']['site_id'],
                        'type' => 'update_site_settings',
                        'site_name' => $site_name,
                        'site_url' => $site_url,
                        'site_image' => $site_image
                    );

                    $result = Api_class::updateSiteSettings($api_key, $data);
                    // pe api error during update.
                    if(isset($result['success']) && $result['success'] == false) {
                        $pe_api_error = !empty($result['message']) ? $result['message'] : 'something went wrong while updating your site settings';
                    }

                    $pe_session['tabdata']['site_info']['site_name'] = $site_name;
                    $pe_session['tabdata']['site_info']['site_url'] = $site_url;
                    $pe_session['tabdata']['site_info']['site_image'] = $site_image;

                    if(!empty($pe_session['tabdata']['site_info']['site_name'])) {
                        $pushengage_settings['site_name'] = $pe_session['tabdata']['site_info']['site_name'];
                        self::update($pushengage_settings);
                    }
                }


                 /*
                 * Update profile information from the general settings screen.
                 */
                if (!empty($_POST['action']) && $_POST['action']=="update_profile") {
                    $tab = "gSettings";
                    $data = array (
                        'site_id' => $pe_session['appdata']['site_id'],
                        'type'	  => 'update_profile_settings',
                        // remove multiple backward slash issue before spcial characters.
                        'user_name'	  => sanitize_text_field(str_replace('\\','',$_POST['user_name'])),
                        'timezones'	  => sanitize_text_field($_POST['timezones'])
                    );

                    $result = Api_class::updateProfileSettings($api_key, $data);
                    // pe api error during update.
                    if(isset($result['success']) && $result['success'] == false) {
                        $pe_api_error = !empty($result['message']) ? $result['message'] : 'something went wrong while updating your profile settings';
                    }

                    $pe_session['tabdata']['user_info']['user_name'] = sanitize_text_field(str_replace('\\','',$_POST['user_name']));
                    $pe_session['tabdata']['timezone_info']['option_value'] = sanitize_text_field($_POST['timezones']);
                }


                /*
                 * Update optin setting information from the subscription dialogbox screen.
                 */
                if (!empty($_POST['action']) && $_POST['action'] == "update_optin_settings") {
                    $tab = "subDialogbox";

                    foreach ($_POST as $key => $value) {
                        if(gettype($_POST[$key]) == "array") {
                            $_POST[$key]= array_map(function($v) {
                            return trim(strip_tags($v));
                            }, $_POST[$key]);

                        } else {
                            $_POST[$key] = strip_tags($_POST[$key]);
                        }
                    }

                    $optin_segments = !empty( $_POST['segments'] ) ? json_encode( array_map( 'sanitize_text_field', $_POST['segments'] ) ) : '[]';
                    if(isset($_POST['switch-site-type']) && $_POST['switch-site-type'] == "on") {

                    if(isset($_POST['optin_sw_support']) && $_POST['optin_sw_support'] == "on" && !in_array($_POST['optin_type'], array(4,5))) {
                        $quick_install = false;
                    } else {
                        $quick_install = true;
                    }

                    $optindata = array (
                        'desktop'=> array (
                            'http' => $pe_session['tabdata']['optin_settings_data']->desktop->http,
                            'https' => array (
                                'optin_delay' => intval(sanitize_text_field($_POST['optin_delay'])),
                                'optin_type' => intval(sanitize_text_field($_POST['optin_type'])),
                                'optin_title' => sanitize_text_field($_POST['optin_title']),
                                'optin_allow_btn_txt' => sanitize_text_field($_POST['optin_allow_btn_txt']),
                                'optin_close_btn_txt' => sanitize_text_field($_POST['optin_close_btn_txt']),
                                'optin_font' => '',
                                'optin_sw_support' => $quick_install,
                                'optin_segments' => $optin_segments
                            )
                        ),
                        'mobile' => array (
                            'http' => $pe_session['tabdata']['optin_settings_data']->desktop->http,
                            'https' => array (
                                'optin_delay' => intval(sanitize_text_field($_POST['optin_delay'])),
                                'optin_type' => intval(sanitize_text_field($_POST['optin_type'])),
                                'optin_title' => sanitize_text_field($_POST['optin_title']),
                                'optin_allow_btn_txt' => sanitize_text_field($_POST['optin_allow_btn_txt']),
                                'optin_close_btn_txt' => sanitize_text_field($_POST['optin_close_btn_txt']),
                                'optin_font' => '',
                                'optin_sw_support' => $quick_install,
                                'optin_segments' => $optin_segments
                            )
                        ),
                        'intermediate' => $pe_session['tabdata']['optin_settings_data']->intermediate
                    );

                } else {
                    $optindata = array(
                        'desktop' => array(
                            'http' => array(
                                'optin_delay' => intval(sanitize_text_field($_POST['optin_delay'])),
                                'optin_type' => intval(sanitize_text_field($_POST['optin_type'])),
                                'optin_title' => sanitize_text_field($_POST['optin_title']),
                                'optin_allow_btn_txt' => sanitize_text_field($_POST['optin_allow_btn_txt']),
                                'optin_close_btn_txt' => sanitize_text_field($_POST['optin_close_btn_txt']),
                                'optin_font' => '',
                                'optin_segments' => $optin_segments
                            ),
                            'https' => $pe_session['tabdata']['optin_settings_data']->desktop->https
                        ),
                        'mobile' => array (
                            'http' => array (
                                'optin_delay' => intval(sanitize_text_field($_POST['optin_delay'])),
                                'optin_type' => intval(sanitize_text_field($_POST['optin_type'])),
                                'optin_title' => sanitize_text_field($_POST['optin_title']),
                                'optin_allow_btn_txt' => sanitize_text_field($_POST['optin_allow_btn_txt']),
                                'optin_close_btn_txt' => sanitize_text_field($_POST['optin_close_btn_txt']),
                                'optin_font' => '',
                                'optin_segments' => $optin_segments
                            ),
                            'https' => $pe_session['tabdata']['optin_settings_data']->desktop->https
                        ),
                        'intermediate'=> $pe_session['tabdata']['optin_settings_data']->intermediate
                    );
                   }

                   if(isset($_POST['switch-site-type']) && $_POST['switch-site-type'] == "on") {
                      $site_type = 'https';
                   }
                   else {
                      $site_type = 'http';
                   }

                   $data = array (
                      'option_data' => $optindata,
                      'site_type' => $site_type
                   );

                   $result = Api_class::updateSubscriptionboxSettings($api_key, $data);

                   // pe api error during update.
                   if(isset($result['success']) && $result['success'] == false) {
                      $pe_api_error = !empty($result['message']) ? $result['message'] : 'something went wrong while updating your subscription dialogbox settings';
                   }

                   $pe_session['tabdata']['optin_settings'] = json_encode($optindata);
                   $pe_session['tabdata']['optin_settings_data'] = json_decode(json_encode($optindata));
                   $pe_session['tabdata']['site_type'] = $site_type;
                }


                /*
                 * Update intermediate page information from the subscription dialogbox screen.
                 */
                if (!empty($_POST['action']) && $_POST['action']=="update_optin_intermediate_page_settings") {
                    $tab = "subDialogbox";

                    $_POST = array_map(function($v) {
                        return trim(strip_tags($v));
                    }, $_POST);

                    $optindata = array (
                        'desktop' => $pe_session['tabdata']['optin_settings_data']->desktop,
                        'mobile'=> $pe_session['tabdata']['optin_settings_data']->mobile,
                        'intermediate'=> array (
                            'page_heading' => sanitize_text_field(str_replace('\\','',$_POST['page_heading'])),
                            'page_tagline'=> sanitize_text_field(str_replace('\\','',$_POST['page_tagline']))
                        )
                    );

                    $data = array(
                        'site_id' => $pe_session['appdata']['site_id'],
                        'type' => 'update_optin_settings',
                        'option_data' => $optindata
                    );

                    $result = Api_class::updateOptinSettings($api_key, $data);
                    // pe api error during update.
                    if(isset($result['success']) && $result['success'] == false) {
                        $pe_api_error = !empty($result['message']) ? $result['message'] : 'something went wrong while updating your subscription intermediate page settings';
                    }

                    $pe_session['tabdata']['optin_settings'] = json_encode($optindata);
                    $pe_session['tabdata']['optin_settings_data'] = json_decode(json_encode($optindata));
                }


                /*
                 * Update welcome notification settings data from the welcome notification settings screen.
                 */
                if (!empty($_POST['action']) && $_POST['action']=="save_welcome_notification") {
                    $tab = "welcome_notification";
                    if(isset($_POST['welcome_enabled'])) {
                        if($_POST['welcome_enabled']=="true") {
                            $welcome_enabled = "true";
                        } else {
                            $welcome_enabled = null;
                        }
                    }

                    $title = sanitize_text_field(str_replace('\\','',$_POST['notification_title']));
                    $message = sanitize_text_field(str_replace('\\','',$_POST['notification_message']));
                    $url = sanitize_text_field($_POST['display_notification_url']);
                    $data = array (
                        'site_id' => $pe_session['appdata']['site_id'],
                        'type' => 'update_welcome_notification',
                        'notification_title' => $title,
                        'notification_message' => $message,
                        'notification_url'	=> $url,
                        'welcome_enabled'	=> $welcome_enabled
                    );

                    $result = Api_class::updateWelcomeNotification($api_key, $data);
                    // pe api error during update.
                    if(isset($result['success']) && $result['success'] == false) {
                        $pe_api_error = !empty($result['message']) ? $result['message'] : 'something went wrong while updating your welcome notification settings';
                    }

                    $wc_data = array (
                        'notification_title' => $title,
                        'notification_message' => $message,
                        'notification_url' => $url,
                        'welcome_enabled' => $welcome_enabled
                    );
                    // to show the updated data instantly after saving.
                    $pe_session['tabdata']['welcome_note_data'] = json_decode(json_encode($wc_data));
                }


                /*
                 * create automatic segmentation from the automatic segmentation screen.
                 */
                if (!empty($_POST['action']) && ($_POST['action'] == "create_automatic_segmentation" || $_POST['action'] == "update_automatic_segmentation")) {
                    $tab = "segmentation";
                    $formated_segment_data = self::formatAutoSegmentRequestData($_POST);

                    // in case of "update automatic segmentation", "segment_id" is needed.
                    if($_POST['action'] == "update_automatic_segmentation") {
                        $formated_segment_data['segment_id'] = (int)$_POST['pe_segment_id'];
                        $result = Api_class::updateAutomaticSegmentation($api_key, json_encode($formated_segment_data));
                    } else {
                        $result = Api_class::createAutomaticSegmentation($api_key, json_encode($formated_segment_data));
                    }

                    // pe api error during create/update.
                    if(isset($result['success']) && $result['success'] == false) {
                        $pe_api_error = !empty($result['message']) ? $result['message'] : "something went wrong while creating/updating automatic segmentation.";
                        if(!empty($result['error']['message'])) {
                            $pe_api_error = $result['error']['message'];
                        }
                    }

                    // show the updated data in the UI of auto-segment list after create or update auto segment.
                    $automatic_segmentation_data = Api_class::getAutomaticSegmentList($api_key);

                    // pe api call getting data/error.
                    if(empty($automatic_segmentation_data) || ( isset($automatic_segmentation_data['success']) && $automatic_segmentation_data['success'] == false ) ||  $automatic_segmentation_data == NULL) {
                        $pe_api_error = !empty($automatic_segmentation_data['message']) ? $automatic_segmentation_data['message'] : 'something went wrong while getting your automatic segmentation settings.';
                    } else {
                        $pe_session['tabdata']['automatic_segment'] = $automatic_segmentation_data;
                    }
                }

                $menu_active_key=true;

            }

            if(!empty($pe_session) && isset($pe_session['check_auth']['success'])) {
                $menu_active_key =false;
            }

            require_once( PUSHENGAGE_PLUGIN_DIR . '/views/admin.php' );
        }

        // getting pushengage site data.
        public static function getSiteData($api_key) {
            if(empty($pe_session['sitedata'])) {
                $sitedata = Api_class::getSiteinfo($api_key);
            } else {
                $sitedata = $pe_session['sitedata'];
            }

            return $sitedata;
        }

        // updating pushengage options row.
        public static function update_settings($data) {
            if(!empty($data)) {
                $pushengage_settings = self::pushengage_settings();
                foreach($data as $key => $value){
                    $pushengage_settings[$key] = $value;
                }

                return update_option('pushengage_settings', $pushengage_settings);
            }

            return true;
        }


        /*
         * inserting pushengage client cdn code in the wordpress sites.
         *  also update the site_key in the wordpress local DB, if not present in case of old users.
         */
        public static function print_pe_clientcdn_script() {
            $pushengage_settings = self::pushengage_settings();
            if(empty($pushengage_settings)) {
                return;
            }

            $api_key = $pushengage_settings['appKey'];
            $site_key = $pushengage_settings['site_key'];
            $site_id = $pushengage_settings['site_id'];

            // handling old users,having api key but no site_key present in his wp local db.
            // also update $site_id, if "site_id" is not present. we are using site_id for uniquely distinguish the error log.
            if(!empty($api_key)) {

                if(empty($site_key) || empty($site_id)) {
                    $get_site_data = self::getSiteData($api_key);
                    if(empty($site_key)) {
                        $site_key = $get_site_data[0]['site_key'];
                        $pushengage_settings['site_key'] = $site_key;
                    }

                    if(empty($site_id)) {
                        $site_id = $get_site_data[0]['site_id'];
                        $pushengage_settings['site_id'] = $site_id;
                    }

                    self::update($pushengage_settings);
                }
            }

            if(!empty($api_key) && !empty($site_key)) {
                $pe_dynamic_js = 'https://clientcdn.pushengage.com/core/'.$site_key.'.js';
                wp_enqueue_script('pushengage-core', $pe_dynamic_js, false, false, true);

            } else {
                wp_enqueue_script('pushengage-core-debug', 'https://clientcdn.pushengage.com', false, false, true);
                $script =  "// pe api/site key not found";
                wp_add_inline_script('pushengage-core-debug', $script, 'before');
            }
        }

        /**
         * Insert segment addition script in the front end for category based segmentation.
         * 
         * @since 3.2.0
         */
        public static function print_pe_segment_addition_script() {
            $pushengage_settings    = self::pushengage_settings();
            $category_segmentation  = json_decode( $pushengage_settings['category_segmentation'] );

            if ( !is_singular( 'post' ) || empty( $category_segmentation ) || !$category_segmentation->enabled  || empty( $category_segmentation->settings ) ) {
                return;
            }

            foreach ( $category_segmentation->settings as $category_segmentation_setting ) {
                if( !empty( $category_segmentation_setting->category_name ) && 
                    !empty( $category_segmentation_setting->segment_name ) && 
                    !empty( $category_segmentation_setting->segment_id ) && 
                    1 === $category_segmentation_setting->status && 
                    has_category( $category_segmentation_setting->category_name ) )
                {
                    $script= '
                    (function() {
                        try {
                            var categorySegmentationSetting = '.json_encode($category_segmentation_setting).';
                            var segmentsObj  = {};
                            var subscribedSegment = {};
                            var segmentAddition = false;
                   
                            var pushSegments = localStorage.getItem("PushSegments");
                            if(pushSegments) {
                                subscribedSegment = JSON.parse(pushSegments);
                            }

                            if(!subscribedSegment.hasOwnProperty(categorySegmentationSetting.segment_id)) {
                                segmentsObj[categorySegmentationSetting.segment_id] = categorySegmentationSetting.segment_name;
                                segmentAddition = true;
                            }

                            if(!segmentAddition) {
                                return;
                            }

                            window._peq.push(["add-to-segment", categorySegmentationSetting.segment_name, function(res) {
                                if(res.statuscode === 1 || res.statuscode === 2) {
                                    _pe.addSegmentsInStorage(segmentsObj);
                                }
                            }])

                        } catch(e) {
                            console.log(e);
                        }
                    }());';

                    wp_add_inline_script( 'pushengage-core', $script, 'after' );
                }
            }
        }

        /*
         * saving pushengage meta data along with the posts to handle draft and scheduled options.
         *  _pe_override : meta tag indicates pushengage send notification checkbox has checked.
         *  _pushengage_custom_text : meta tag indicates pushengage custom notication message is available.
         *  _pe_draft_segments : meta tag used in case of segments are available. _pe_draft_segments meta tag is used
         *                       to prefilled the pushengage override in case of editing and publishing the
         *                       saved post.
         *  _sedule_notification: meta tag used in case of segments are available and post status is ("future" or "inherit")
         *  pe_override_scheduled : status shows, the post is saved for future.
         */
        public static function save_pe_post_meta_data($post_id) {

            if (! current_user_can( 'edit_posts' ) )
            {
                return false;

            } else {
                $no_note = get_post_meta($post_id, '_pe_override', true);

                if (isset($_POST['pushengage-override']) && ! $no_note) {
                    $override_setting = sanitize_text_field($_POST['pushengage-override']);
                    add_post_meta( $post_id, '_pe_override', $override_setting, true );

                } elseif(!isset($_POST['pushengage-override']) && $no_note) {
                    delete_post_meta( $post_id, '_pe_override' );
                }

                if(isset( $_POST['pushengage-custom-msg'])) {
                    update_post_meta( $post_id, '_pushengage_custom_text', sanitize_text_field( $_POST['pushengage-custom-msg'] ) );
                }

                if(isset($_POST['pushengage-override'])) {
                    if(!empty( $_POST['pushengage-categories'] )) {
                        $draft_segments_filter = array_map( 'sanitize_text_field', $_POST['pushengage-categories'] ) ;
                        $draft_segments = implode(" ",$draft_segments_filter);
                        $prev_segments = get_post_meta( $post_id, '_pe_draft_segments', true );
                        update_post_meta( $post_id, '_pe_draft_segments', $draft_segments, $prev_segments);

                    } else {
                        delete_post_meta($post_id, '_pe_draft_segments');
                    }

                    $str = "";
                    if(isset($_POST['pushengage-categories']) && !empty($_POST['pushengage-categories']) && (get_post_status($post_id) == 'future' || get_post_status($post_id) == 'inherit')) {
                        $str_filter = array_map( 'sanitize_text_field', $_POST['pushengage-categories'] );
                        $str = implode(" ", $str_filter);
                        add_post_meta( $post_id, '_sedule_notification', $str, true );
                    }

                    add_post_meta( $post_id, 'pe_override_scheduled', 1, true );

                } else {
                    delete_post_meta($post_id, '_pe_draft_segments');
                    delete_post_meta($post_id, '_sedule_notification');
                    delete_post_meta($post_id, 'pe_override_scheduled');
                }
            }
        }

        /*
         *  send push notifications on publishing of the post or page.
         */
        public static function send_pe_push_notifications( $new_status, $old_status, $post ) {
            // new status always should be "publish"
            if(empty( $post ) || 'publish' !== $new_status || false === self::is_pushengage_active()) {
                return;
            }

            if(!current_user_can('publish_posts') && ! DOING_CRON) {
                return;
            }

            $pushengage_settings = self::pushengage_settings();
            $api_key = $pushengage_settings['appKey'];
            $all_post_types = $pushengage_settings['all_post_types'];
            $use_featured_image = $pushengage_settings['use_featured_image'];
            $appdata = !empty( $pe_session['appdata'] ) ? $pe_session['appdata'] : array();
            $notification_title = !empty($appdata['site_name'])?$appdata['site_name']:$pushengage_settings['site_name'];
            $post_id = $post->ID;
            $post_type = get_post_type( $post );

            // handling all post type option to send push notification.
            if (false === $all_post_types) {
                if('post' !== $post_type) {
                    return;
                }
            }

            // metatag 'pe_timestamp' is used to prevent multiple send within 30 seconds.
            if(get_post_meta($post->ID, 'pe_timestamp',true) >= date("Y-m-d H:i:s")) {
                return;
            }

            // check if, pushengege send notification check box is checked.
            if (isset($_POST['pushengage-override'])) {
                $send_note = true;
                $override = sanitize_text_field( $_POST['pushengage-override'] );
            }

            // check if, it is scheduled post.
            if('publish' === $new_status && 'future' === $old_status) {
                if(get_post_meta( $post_id, 'pe_override_scheduled', true )) {
                    $send_note = true;
                }

                $override = get_post_meta( $post_id, '_pe_override', true );
                $custom_headline = get_post_meta( $post_id, '_pushengage_custom_text', true );
                $segments=explode(" ",get_post_meta( $post_id, '_sedule_notification', true ));
                $seg_array = array_filter($segments);
                if(empty($seg_array)) {
                    $segments=false;
                }
            }

            // check if,  custom message is present.
            if(isset($_POST['pushengage-custom-msg']) && ! empty($_POST['pushengage-custom-msg'])) {
                $custom_headline = sanitize_text_field( $_POST['pushengage-custom-msg'] );
            }

            // check if, notfication is segmented notification.
            if(isset($_POST['pushengage-categories']) && !empty($_POST['pushengage-categories'])) {
                $segments = array_map( 'sanitize_text_field', $_POST['pushengage-categories'] );
            }


            /*
             * check if $send_note is true and  $override is present
             * then, send notificaion according to the payload.
             */
            if(!empty( $send_note) && !empty($override)) {
                $adv_options = array();
                if(!empty($custom_headline)) {
                    $notification_message = stripslashes( $custom_headline ) ;
                } else {
                    $notification_message = sanitize_text_field(get_the_title( $post_id ));
                }

                $notification_url = get_permalink( $post_id );

                /**
                 * if $use_featured_image = 0, then use feature image as a big image
                 * and site_image as a notification icon image.
                 * else, use feature image as an notification icon image.
                 */
                if(empty($use_featured_image)) {
                    if(has_post_thumbnail($post_id)) {
                        $raw_big_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id),array(364,180));
                        if(!empty($raw_big_image)) {
                            $adv_options['big_image_url'] = $raw_big_image[0];
                        }
                    }

                    $image_url = !empty($appdata['site_image'])?$appdata['site_image']:'';

                } else {
                    if(has_post_thumbnail($post_id)) {
                        $raw_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id));
                        if(!empty($raw_image)) {
                            $featured_image_url = $raw_image[0];
                        }
                        $image_url = !empty($featured_image_url)?$featured_image_url:'';
                    }

                    if(empty($image_url)) {
                        $image_url = !empty($appdata['site_image'])?$appdata['site_image']:'';
                    }
                }

                // add UTM params to the notifiication url
                if(isset($pushengage_settings['utmcheckbox'])
                   && !empty($pushengage_settings['utmcheckbox'])
                   && isset($pushengage_settings['utm_source'])
                   && isset($pushengage_settings['utm_medium'])
                   && isset($pushengage_settings['utm_campaign'])
                )
                {
                    $notification_url = add_query_arg( array(
                        'utm_source' => rawurlencode($pushengage_settings['utm_source']),
                        'utm_medium' => rawurlencode($pushengage_settings['utm_medium']),
                        'utm_campaign' => rawurlencode($pushengage_settings['utm_campaign'])
                    ), $notification_url);

                } else {
                    $notification_url = add_query_arg( array(
                        'utm_source' => 'pushengage',
                        'utm_medium' => 'push_notification',
                        'utm_campaign' => 'pushengage'
                    ), $notification_url);
                }

                if(empty($appdata['site_name'])) {
                    $appdata['site_name'] = $pushengage_settings['site_name'];
                }

                // send  post_id, old status and new status as query params of send notificaiton api for debugging purpose.
                $adv_options['post_id'] = $post_id;
                $adv_options['old_status'] = $old_status;
                $adv_options['new_status'] = $new_status;

                // add or update post metadata "pe_timestamp"  by 30 second, whenever notification sent.
                if(get_post_meta($post->ID, 'pe_timestamp',true)) {
                    update_post_meta($post->ID, 'pe_timestamp', date("Y-m-d H:i:s", time() + 30));
                } else {
                    add_post_meta($post->ID, 'pe_timestamp', date("Y-m-d H:i:s", time() + 30), true);
                }

                $result = Api_class::sendNotification($api_key, $notification_title, $notification_message, $notification_url, $segments, $image_url, $adv_options );
            }
        }

        // check wordpress version to show the warning in the pushengage admin screen.
        public static function checkWPVersion() {
            $user_version = self::$pushengage_version;

            // get current wordpress information
            $plugin_name = 'pushengage';
            $args = array (
                'timeout' => 15,
                'redirection' => 30,
            );

            $current_version = get_transient('pe_plugin_version');
            if(empty($current_version) || $user_version != $current_version ) {
                $url = 'http://api.wordpress.org/plugins/info/1.0/' . $plugin_name . '.json';
                $response = wp_remote_get( $url, $args );

                if(is_array($response) && !empty($response['body'])) {
                    $plugin_info = json_decode($response['body'] );
                }

                $current_version = !empty($plugin_info->version) ? $plugin_info->version : '';
                // putting expiry of one day(i.e, 24 hours).
                set_transient('pe_plugin_version', $current_version, 86400);
            }

            if(!empty($user_version) && !empty($current_version) && $user_version != $current_version) {
                return false;
            }

            return true;
        }

        public static function init_user_options() {
            if(!is_user_logged_in()){
                return;
            }

            // adding a checkbox in the right side of the screen to stop or send notification on post pubish action.
            // it can be used to override the default or saved setting of sending posr.
            add_action( 'add_meta_boxes', array( __CLASS__, 'add_pe_notification_override_meta_box' ) );
            // add an input field in the bottom side of the screen to provide functionality of adding custon notification title.
            add_action( 'add_meta_boxes', array( __CLASS__, 'add_pe_custom_notification_message_field' ), 10, 2 );
            // action hook, to handle the case of draft and schedule post to send notification.
            add_action( 'save_post', array( __CLASS__, 'save_pe_post_meta_data' ) );

            if(self::can_edit_plugin_options()) {
                add_action( 'admin_init', array( __CLASS__, 'validate_csrf_token') );
                add_action( 'admin_init', array( __CLASS__, 'pushengage_save_settings' ) );
                add_action( 'admin_menu', array( __CLASS__, 'add_pe_admin_menu' ) );
            }

        }

        public static function validate_csrf_token() {
            if(!empty($_POST)
                && $_GET['page'] == 'pushengage-admin'
                && (
                    ! isset( $_POST['pe_token'] )
                    || ! wp_verify_nonce($_POST['pe_token'], 'pe_token')
                    || ! self::can_edit_plugin_options()
                )
            )
            {
                $tab = !empty($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'gSettings';
                $url = esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab=' . $tab . '&status=failed'));
                wp_redirect($url);
                exit;
            }
        }

        // check user privileges to edit plugin.
        private static function can_edit_plugin_options() {
            return current_user_can('manage_options');
        }

        // get ISO timezone, if user has saved the timezone in UP and UM form.
        public static function getIsoTimezone($timezone) {
            $timezone_mapping = [
                "UM12" => "UTC",
                "UM11" => "Pacific/Niue",
                "UM10" => "Pacific/Rarotonga",
                "UM95" => "Pacific/Marquesas",
                "UM9" => "Pacific/Gambier",
                "UM8" => "Pacific/Pitcairn",
                "UM7" => "America/Creston",
                "UM6" => "America/Belize",
                "UM5" => "America/Chicago",
                "UM45"=>"America/Caracas",
                "UM4"=>"America/St_Vincent",
                "UM35"=>"America/St_Johns",
                "UM3"=>"America/Argentina/La_Rioja",
                "UM2"=>"America/Noronha",
                "UM1"=>"Atlantic/Cape_Verde",
                "UTC"=>"UTC",
                "UP1"=>"Africa/Algiers",
                "UP2"=>"Europe/Amsterdam",
                "UP3"=>"Africa/Addis_Ababa",
                "UP35"=>"Asia/Tehran",
                "UP4"=>"Europe/Samara",
                "UP45"=>"Asia/Kabul",
                "UP5"=>"Asia/Karachi",
                "UP55"=>"Asia/Kolkata",
                "UP575"=>"Asia/Kathmandu",
                "UP6"=>"Asia/Dhaka",
                "UP65"=>"Asia/Yangon",
                "UP7"=>"Asia/Vientiane",
                "UP8"=>"Asia/Macau",
                "UP875"=>"Australia/Eucla",
                "UP9"=>"Asia/Tokyo",
                "UP95"=>"Australia/Broken_Hill",
                "UP10"=>"Australia/Currie",
                "UP105"=>"Australia/Lord_Howe",
                "UP11"=>"Asia/Srednekolymsk",
                "UP115"=>"Pacific/Norfolk",
                "UP12"=>"Pacific/Fiji",
                "UP1275"=>"Pacific/Chatham",
                "UP13"=>"Pacific/Tongatapu",
                "UP14"=>"Pacific/Kiritimati",
            ];

            if(isset($timezone_mapping[$timezone])) {
                return $timezone_mapping[$timezone];
            }

            try {
                $iso_timezone = new DateTimeZone($timezone);
                return $timezone;

            } catch (Exception $e) {
                return 'UTC';
            }
        }

        // dropdown list of timezone in ISO form along with its offset values
        public static function select_Timezone($selected = '') {
            $iso_timezone_list = timezone_identifiers_list();
            $timezone_offsets = array();
            foreach($iso_timezone_list as $timezone) {
                $tz= new DateTime('now', new DateTimeZone($timezone));
                $timezone_offsets[$timezone]=$tz->format('P');
            }

            asort($timezone_offsets);
            $select= '<select name="timezones" class="form-control">';
            foreach($timezone_offsets as $iso_timezone => $offset) {
                $select .= '<option value="'.$iso_timezone.'"';
                $select .= ($iso_timezone == $selected ? 'selected' : '');
                $select .= '>'."(GMT".$offset."  ".$iso_timezone.")".'</option>';
            }

            $select .= '</select>';
            return $select;
        }

        /*
         * formatting request data of automatic segmentation to make API call.
         */
        public static function formatAutoSegmentRequestData($form_data) {
            $include_start_pattern_value = array();
            $include_contains_pattern_value = array();
            $include_exact_pattern_value = array();
            $exclude_start_pattern_value = array();
            $exclude_contains_pattern_value = array();
            $exclude_exact_pattern_value = array();
            $segment_criteria = array();
            $pe_segment_page_visit = 0;
            $automatic_segment_data = array();

            // automatic segment name
            if(!empty($form_data['pe_segment_name'])) {
                $automatic_segment_data['segment_name'] = sanitize_text_field($form_data['pe_segment_name']);
            }

            // segment on page visit.
            if(!empty($form_data['pe_segment_page_visit'])) {
                $automatic_segment_data['add_segment_on_page_load'] =  intval(sanitize_text_field($form_data['pe_segment_page_visit']));
            } else {
                $automatic_segment_data['add_segment_on_page_load'] = 0;
            }

            // getting include segment pattern rule from the form data.
            for($i=1; $i <= $form_data['pe_include_segment_count']; $i++) {
                if(!empty($form_data['pe_include_pattern_value_'.$i])) {
                    if($form_data['pe_include_pattern_rule_options_'.$i] == 'start') {
                        array_push( $include_start_pattern_value, sanitize_text_field($form_data['pe_include_pattern_value_'.$i]) );
                    }
                    if($form_data['pe_include_pattern_rule_options_'.$i] == 'contains') {
                        array_push( $include_contains_pattern_value, sanitize_text_field($form_data['pe_include_pattern_value_'.$i]) );
                    }
                    if($form_data['pe_include_pattern_rule_options_'.$i] == 'exact') {
                        array_push( $include_exact_pattern_value, sanitize_text_field($form_data['pe_include_pattern_value_'.$i]) );
                    }
                }
            }

            // getting exclude segment pattern rule from the form data.
            for($i=1; $i <= $form_data['pe_exclude_segment_count']; $i++) {
                if(!empty($form_data['pe_exclude_pattern_value_'.$i])) {
                    if($form_data['pe_exclude_pattern_rule_options_'.$i] == 'start') {
                        array_push( $exclude_start_pattern_value, sanitize_text_field($form_data['pe_exclude_pattern_value_'.$i]) );
                    }
                    if($form_data['pe_exclude_pattern_rule_options_'.$i] == 'contains') {
                        array_push( $exclude_contains_pattern_value, sanitize_text_field($form_data['pe_exclude_pattern_value_'.$i]) );
                    }
                    if($form_data['pe_exclude_pattern_rule_options_'.$i] == 'exact') {
                        array_push( $exclude_exact_pattern_value, sanitize_text_field($form_data['pe_exclude_pattern_value_'.$i]) );
                    }
                }
            }

            $segment_criteria['include'] = array();
            $segment_criteria['exclude'] = array();

            // preparing the include segment data.
            // include : {"rule" : "start", "value": "https://www.abcd.com"}
            if(!empty($include_start_pattern_value)) {
                for($i = 0; $i < count($include_start_pattern_value); $i++) {
                    array_push($segment_criteria['include'], array( 'rule' => 'start', 'value' => $include_start_pattern_value[$i]));
                }
            }

            if(!empty($include_contains_pattern_value)) {
                for($i = 0; $i < count($include_contains_pattern_value); $i++) {
                    array_push($segment_criteria['include'], array( 'rule' => 'contains', 'value' => $include_contains_pattern_value[$i]));
                }
            }

            if(!empty($include_exact_pattern_value)) {
                for($i = 0; $i < count($include_exact_pattern_value); $i++) {
                    array_push($segment_criteria['include'], array( 'rule' => 'exact', 'value' => $include_exact_pattern_value[$i]));
                }
            }
            // preparing the exclude segment data.
            // exclude : {"rule" : "start", "value": "https://www.abcd.com"}
            if(!empty($exclude_start_pattern_value)) {
                for($i = 0; $i < count($exclude_start_pattern_value); $i++) {
                    array_push($segment_criteria['exclude'], array( 'rule' => 'start', 'value' => $exclude_start_pattern_value[$i]));
                }
            }

            if(!empty($exclude_contains_pattern_value)) {
                for($i = 0; $i < count($exclude_contains_pattern_value); $i++) {
                    array_push($segment_criteria['exclude'], array( 'rule' => 'contains', 'value' => $exclude_contains_pattern_value[$i]));
                }
            }

            if(!empty($exclude_exact_pattern_value)) {
                for($i = 0; $i < count($exclude_exact_pattern_value); $i++) {
                    array_push($segment_criteria['exclude'], array( 'rule' => 'exact', 'value' => $exclude_exact_pattern_value[$i]));
                }
            }

            // if include or exclude segment rule is empty, don't send that key to the  server.
            if(empty($segment_criteria['include'])) {
                unset($segment_criteria['include']);
            }

            if(empty($segment_criteria['exclude'])) {
                unset($segment_criteria['exclude']);
            }

            if(!empty($segment_criteria)) {
                $automatic_segment_data['segment_criteria'] =  $segment_criteria;
            }

            return $automatic_segment_data;
        }

    }

?>
