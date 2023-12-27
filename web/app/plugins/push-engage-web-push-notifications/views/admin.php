<?php
// Fetch values from the $pe_session array and assign to the required variables.
if(!empty($pe_session)) {
    $optin_settings                 = !empty( $pe_session['tabdata']['optin_settings_data'] ) ? $pe_session['tabdata']['optin_settings_data'] : '';
    $site_type                      = !empty( $pe_session['tabdata']['site_type'] ) ? $pe_session['tabdata']['site_type'] : '';
    $appdata                        = !empty( $pe_session['tabdata']['site_info'] ) ? $pe_session['tabdata']['site_info'] : '';
    $welcome_note_data              = !empty( $pe_session['tabdata']['welcome_note_data'] ) ? $pe_session['tabdata']['welcome_note_data'] : '';
    $userdata                       = !empty( $pe_session['tabdata']['user_info'] ) ? $pe_session['tabdata']['user_info'] : '';
    $timezone                       = !empty( $pe_session['tabdata']['timezone_info']['option_value'] ) ? $pe_session['tabdata']['timezone_info']['option_value'] : '';
    $segments_data                  = !empty( $pe_session['tabdata']['segments_data'] ) ? $pe_session['tabdata']['segments_data'] : '';
    $automatic_segmentation_data    = !empty( $pe_session['tabdata']['automatic_segment']['segments'] ) ? $pe_session['tabdata']['automatic_segment']['segments'] : '';
    $wp_category_list               = !empty( $pe_session['tabdata']['wp_category_list'] ) ? $pe_session['tabdata']['wp_category_list'] : array();
    $pe_segment_list                = !empty( $pe_session['tabdata']['pe_segment_list'] ) ? $pe_session['tabdata']['pe_segment_list'] : array();
    $category_segmentation_enabled  = !empty( $pe_session['tabdata']['category_segmentation_enabled'] ) ? $pe_session['tabdata']['category_segmentation_enabled'] : false;
    $wp_category_pe_segment_map     = !empty( $pe_session['tabdata']['wp_category_pe_segment_map'] ) ? $pe_session['tabdata']['wp_category_pe_segment_map'] : array();

    if ( isset( $appdata['site_name'] ) && !empty( $appdata['site_name'] ) ) {
        $site_name = esc_html( $appdata['site_name'] );
    } else {
        $site_name = '';
    }

    if ( isset( $optin_settings->intermediate ) ) {
        $intermediate_page_heading = !empty( $optin_settings->intermediate->page_heading ) ? esc_html( $optin_settings->intermediate->page_heading ) : "Click on Allow to get Notifications from '".$site_name."'";
        $intermediate_page_tagline = !empty( $optin_settings->intermediate->page_tagline ) ? esc_html( $optin_settings->intermediate->page_tagline ) : "Get Updates from '".$site_name."' through push notifications";
    }

    function add_special_char( $string_with_special_char ) {
        return str_replace('"', " &quot;", $string_with_special_char);
    }

    if ( isset( $optin_settings->desktop->http ) ) {
        $dialogbox_type         = !empty( $optin_settings->desktop->http->optin_type ) ? $optin_settings->desktop->http->optin_type : 1;
        $optin_title            = !empty( $optin_settings->desktop->http->optin_title ) ? add_special_char( $optin_settings->desktop->http->optin_title ) : "This website '".$site_name."' would like to send push notifications";
        $optin_allow_button     = !empty( $optin_settings->desktop->http->optin_allow_btn_txt ) ? add_special_char( $optin_settings->desktop->http->optin_allow_btn_txt ) : 'Allow';
        $optin_close_button     = !empty( $optin_settings->desktop->http->optin_close_btn_txt ) ? add_special_char( $optin_settings->desktop->http->optin_close_btn_txt ) : 'Close';
        $optin_delay_time       = !empty( $optin_settings->desktop->http->optin_delay ) ? $optin_settings->desktop->http->optin_delay : 0;
        $quick_install          = !empty( $optin_settings->desktop->http->optin_sw_support ) ? $optin_settings->desktop->http->optin_sw_support : '';
        $optin_segments_http    = !empty( $optin_settings->desktop->http->optin_segments ) ? json_decode( $optin_settings->desktop->http->optin_segments ) : '';
    } else {
        $dialogbox_type         = !empty( $optin_settings->desktop->optin_type ) ? $optin_settings->desktop->optin_type: 1;
        $optin_title            = !empty( $optin_settings->desktop->http->optin_close_btn_txt ) ? add_special_char( $optin_settings->desktop->optin_title ) : "This website '".$site_name."' would like to send push notifications";
        $optin_allow_button     = !empty( $optin_settings->desktop->optin_allow_btn_txt ) ? add_special_char( $optin_settings->desktop->optin_allow_btn_txt ) : 'Allow';
        $optin_close_button     = !empty( $optin_settings->desktop->optin_close_btn_txt ) ? add_special_char( $optin_settings->desktop->optin_close_btn_txt ) : 'Close';
        $optin_delay_time       = !empty( $optin_settings->desktop->optin_delay ) ? $optin_settings->desktop->optin_delay : 0;
        $optin_segments_http    = !empty( $optin_settings->desktop->http->optin_segments ) ? json_decode( $optin_settings->desktop->http->optin_segments ) : '';
    }

    if ( isset( $optin_settings->desktop->https ) ) {
        $dialogbox_type_https       = !empty( $optin_settings->desktop->https->optin_type ) ? $optin_settings->desktop->https->optin_type : 1;
        $optin_title_https          = !empty( $optin_settings->desktop->https->optin_title ) ? add_special_char( $optin_settings->desktop->https->optin_title ) : "This website '".$site_name."' would like to send push notifications";
        $optin_allow_button_https   = !empty( $optin_settings->desktop->https->optin_allow_btn_txt ) ? add_special_char( $optin_settings->desktop->https->optin_allow_btn_txt ) : 'Allow';
        $optin_close_button_https   = !empty( $optin_settings->desktop->https->optin_close_btn_txt ) ? add_special_char( $optin_settings->desktop->https->optin_close_btn_txt ) : 'Close';
        $optin_delay_time_https     = !empty( $optin_settings->desktop->https->optin_delay ) ? $optin_settings->desktop->https->optin_delay : 0;
        $quick_install              = !empty( $optin_settings->desktop->https->optin_sw_support ) ? $optin_settings->desktop->https->optin_sw_support : '';
        $optin_segments_https       = !empty( $optin_settings->desktop->https->optin_segments ) ? json_decode( $optin_settings->desktop->https->optin_segments ) : '';
    } else {
        $dialogbox_type_https       = !empty( $optin_settings->desktop->optin_type ) ? $optin_settings->desktop->optin_type : 1;
        $optin_title_https          = !empty( $optin_settings->desktop->optin_title ) ? add_special_char( $optin_settings->desktop->optin_title ) : "This website '".$site_name."' would like to send push notifications";
        $optin_allow_button_https   = !empty( $optin_settings->desktop->optin_allow_btn_txt ) ? add_special_char( $optin_settings->desktop->optin_allow_btn_txt ) : 'Allow';
        $optin_close_button_https   = !empty( $optin_settings->desktop->optin_close_btn_txt ) ? add_special_char( $optin_settings->desktop->optin_close_btn_txt ) : 'Close';
        $optin_delay_time_https     = !empty( $optin_settings->desktop->optin_delay ) ? $optin_settings->desktop->optin_delay : 0;
        $optin_segments_https       = !empty( $optin_settings->desktop->https->optin_segments ) ? json_decode( $optin_settings->desktop->https->optin_segments ) : '';
    }
}


if(empty($pe_session['menu_active_key'])) {
    ?>
    <script type="text/javascript">var $ = jQuery.noConflict();</script>
    <?php
    wp_enqueue_style('bootstrap', PUSHENGAGE_URL . 'css/bootstrap.css', array(), "", "all");
    wp_enqueue_style('style', PUSHENGAGE_URL . 'css/pe-style.css', array(), "", "all");
    wp_enqueue_style( 'font-awesome.min', PUSHENGAGE_URL . 'css/font-awesome.min.css', array(), "", "all" );
    ?>

    <?php
    // selection of tab
    $pe_setup_tab = "";
    $pe_instruction_tab = "";

    switch (!empty($tab_start) ? $tab_start : '') {
        case "instruct" :
            $pe_instruction_tab = "active arrow_box";
            break;
        case "setup":
            $pe_setup_tab = "active arrow_box";
            break;
        default:
            $pe_instruction_tab = "active arrow_box";
    }

    $pe_is_error = false;
    if($is_api_key_verification_error == true && empty($pe_session['menu_active_key'])) {
       $pe_is_error = true;
     }

    ?>
    <div class="container pe-container">
        <div class="row">
            <div class="col-sm-12">
                <div role="tabpanel">
                    <ul class="nav nav-tabs pe-nav-tab" role="tablist">
                        <li role="presentation" class="<?php echo $pe_instruction_tab; ?>">
                            <a href="#" aria-controls="instruction" role="tab" data-toggle="tab">Instruction</a>
                        </li>
                        <li role="presentation" class="<?php echo $pe_setup_tab; ?>">
                            <a href="#" aria-controls="setup" role="tab" data-toggle="tab">Setup</a>
                        </li>
                    </ul>

                    <div class="tab-content col-sm-12 pe-instruction-content">
                        <div role="tabpanel" class="<?php
                        if ($pe_instruction_tab == "active arrow_box" && empty($pe_session['tabdata']['success'])) {
                            echo "tab-pane active";
                        } else {
                            echo "tab-pane";
                        }
                        ?>" id="instruction">
                            <div role="tabpanel" class="tab-pane active">
                                <div class="col-sm-12 no-padding">
                                    <h1 class="pe-header inst-header center-text">HOW TO GET MY API KEY</h1>
                                </div>

                                <div class="col-sm-12 pe-inst-step-box">
                                    <div role="tabpanel">
                                        <ul class="nav nav-tabs nav-line pe-inst-step-tab" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#" aria-controls="step1" role="tab" data-toggle="tab">STEP 1</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#" aria-controls="step2" role="tab" data-toggle="tab">STEP 2</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#" aria-controls="step3" role="tab" data-toggle="tab">STEP 3</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content pe-inst-step-content">
                                            <div role="tabpanel" class="tab-pane active" id="step1">
                                                <h2 class="pe-inst-step-title">Open your PushEngage
                                                    <a class="link-decoration" href="https://app.pushengage.com" target="_blank">Dashboard</a> and click on Settings
                                                    <span class="for-symbol">&#x2192;</span> Site Settings  <span class="for-symbol">&#x2192;</span> API Keys
                                                </h2>
                                                <img src="<?php echo PUSHENGAGE_PLUGIN_URL . "images/ins1.png" ?>" alt="instruction image 1" class="img-responsive">
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="step2">
                                                <h2 class="pe-inst-step-title">
                                                    To generate API Key click on Generate a new Api Key
                                                </h2>
                                                <img src="<?php echo PUSHENGAGE_PLUGIN_URL . "images/ins2.png" ?>" alt="instruction image 2" class="img-responsive">
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="step3">
                                                <h2 class="pe-inst-step-title">
                                                    Copy generate API Key, open Setup tab and paste it
                                                </h2>
                                                <img src="<?php echo PUSHENGAGE_PLUGIN_URL . "images/ins3.png" ?>" alt="instruction image 3" class="img-responsive">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane col-sm-12 pe-setup-content <?php if($pe_is_error == true) {
                           echo "active";
                        } ?>" id="setup">
                            <?php if ($pe_is_error == true) { ?>
                                <div class="row" style="margin-top: 25px;">
                                    <div class="col-md-3"></div>
                                    <div class="alert alert-danger alert-dismissable col-md-6">
                                        <?php
                                        if(!empty($pe_session['message']) && $pe_session['error_code'] !== "1001") {
                                            echo $pe_session['message'];

                                        } else {
                                            echo "API Key is invalid. Please try again.";
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <form class="text-center" name="login-form" id="login_form" method="post" action="admin.php?page=pushengage-admin">
                                <h1 class="pe-header inst-header">
                                    Login
                                </h1>
                                <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                <input type="hidden" name="action" value="update_api_key"/>
                                <input type="text" name="api_key" id="api_key" required placeholder="Enter Your API Key" autocomplete="off" >
                                <div style="padding-top:15px;">
                                    <input type="submit" name="form_submit" value="Submit" class="button button-primary" style="background: #0565c7;font-size: 15px;">
                                    <div style="padding-top:15px;text-align: left">
                                        If you do not have an API Key, then please register at
                                        <a href="https://www.pushengage.com/pricing" target="_blank">PushEngage</a>,
                                        and obtain your key from
                                        <a href="https://app.pushengage.com/settings/api-keys" target="_blank">Dashboard</a>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php   include_once('footer.php'); ?>
    </div>

    <script>
    jQuery(document).ready(function () {

        // Change tab
        jQuery(document).on('click','.pe-nav-tab>li>a',function() {

            // show and hide div
            jQuery('.pe-instruction-content>.tab-pane').removeClass('active');
            var targetDiv=jQuery(this).attr('aria-controls')
            jQuery('#'+targetDiv).addClass('active');

            // add and remove active class from tab
            jQuery('.pe-nav-tab>li').removeClass('active');
            var targetLink=jQuery(this).parent('li')
            jQuery(targetLink).addClass('active')
        })

        // Change instruction sub menu tab
        jQuery(document).on('click','.pe-inst-step-tab>li>a',function() {

            // show and hide div
            jQuery('.pe-inst-step-content>.tab-pane').removeClass('active');
            var targetDiv=jQuery(this).attr('aria-controls')
            jQuery('#'+targetDiv).addClass('active');

            // add and remove active class from tab
            jQuery('.pe-inst-step-tab>li').removeClass('active');
            var targetLink=jQuery(this).parent('li')
            jQuery(targetLink).addClass('active')
        })

    });

</script>

<?php } else if (isset($pe_session['check_auth']['success']) && $pe_session['check_auth']['success'] == false && !empty($pe_session['check_auth']['message'])) {
            include_once("disable_user.php");
      } else {
?>

    <div class="container-widget">

        <?php include_once( 'header.php' ); ?>
        <?php
        // selection of tab
        $pe_general_settings            = "";
        $pe_dialogbox_settings          = "";
        $pe_welcome_noti_settings       = "";
        $pe_segment_settings            = "";
        $pe_category_segment_settings   = "";

        if ( !empty( $_GET['tab'] ) && $_GET['tab'] )
            $tab = sanitize_text_field( $_GET['tab'] );
        else
            $tab = 'gSettings';

        switch ( $tab ) {
            case "gSettings" :
                $pe_general_settings = "active";
                break;
            case "subDialogbox":
                $pe_dialogbox_settings = "active";
                break;
            case "welcome_notification":
                $pe_welcome_noti_settings = "active";
                break;
            case "segmentation":
                $pe_segment_settings = "active";
                break;
            case "category-segmentation":
                $pe_category_segment_settings = "active";
                break;
            default:
                $pe_general_settings = "active";
        }
        ?>
        <!-- tab style implementation-->
        <div class="row">
            <?php if (!Pushengage::checkWPVersion()) { ?>
                <div id="message" class="update-message notice inline notice-warning notice-alt" style="margin-bottom:15px;">
                     <p>There is a new version of PushEngage plug-in available. Please update now for new features. </p>
                </div>
            <?php } ?>
            <?php if (!empty($pe_session['check_auth']['message'])) { ?>
                <div id="message" class="update-message notice inline notice-warning notice-alt" style="margin-bottom:15px;">
                    <p> <?php echo $pe_session['check_auth']['message']; ?> </p>
                </div>
            <?php } ?>

            <ul class="nav nav-tabs pe-nav-tab" role="tablist">
                <li class="<?php echo $pe_general_settings; ?>" role="presentation" id="li_pe_general_setting">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab=gSettings' ) ); ?>">General Settings</a>
                </li>
                <li class="<?php echo $pe_dialogbox_settings; ?>" role="presentation" id="li_pe_subscription_dialogbox">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab=subDialogbox' ) ); ?>">Subscription Dialogbox</a>
                </li>
                <li class="<?php echo $pe_welcome_noti_settings; ?>" role="presentation" id="li_pe_welcome_notification">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab=welcome_notification' ) ); ?>">Welcome Notification Settings</a>
                </li>
                <li class="<?php echo $pe_segment_settings; ?>" role="presentation" id="li_pe_automatic_segmentation">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab=segmentation' ) ); ?>">Automatic Segmention</a>
                </li>
                <li class="<?php echo $pe_category_segment_settings; ?>" role="presentation" id="li_pe_category_segmentation">
                    <a href="<?php echo esc_url_raw( admin_url( 'admin.php?page=pushengage-admin&tab=category-segmentation' ) ); ?>">Category Segmention</a>
                </li>
            </ul>


            <div class="tab-content">
                <?php if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'failed') { ?>
                    <div class="updated notice error is-dismissible" style="margin-bottom:15px;">
                        <p style="color:red" >An error occured. Try again. </p>
                     </div>
                <?php } else if(!empty($pe_api_error)) { ?>
                    <div class="updated notice error is-dismissible" style="margin-bottom:15px;">
                        <p style="color:red" ><?php echo $pe_api_error; ?></p>
                     </div>
                <?php } ?>
                <!--General settings -->
                <div id="gSettings" class="<?php
                if ($pe_general_settings == "active") {
                    echo " tab-pane fade in active";
                } else {
                    echo "tab-pane fade";
                }
                ?>">
                    <div class="row">
                        <!--start site settings-->
                        <div class="col-md-6 col-lg-6">
                            <div class="panel panel-default">
                                <div class="form-wrap box box-primary  box-body" style="width:50%;">
                                    <form id="general_settings" method="post" class="validate" action="admin.php?page=pushengage-admin&tab=gSettings">
                                        <div class="box-header with-border">
                                            <div class="panel-title">Site Settings</div>
                                        </div>
                                        <div class="form-field form-required">
                                            <label for="tag-name"><strong>API Key</strong></label>
                                            <input type="text" name="pushengage-apikey" style="width:400px" class="form-control" readonly placeholder="Enter API Key" class="form-control"
                                                   value="<?php if ($pushengage_settings['appKey']) echo esc_html($pushengage_settings['appKey']); ?>"/>
                                        </div>
                                        <?php if ($pushengage_settings['appKey']) { ?>
                                            <div class="form-field form-required">
                                                <table>
                                                    <?php if(!empty($appdata['site_image'])){?>
                                                    <tr>
                                                        <td rowspan=4 style="padding-right: 35px;">
                                                            <img src="<?php echo esc_url($appdata['site_image']); ?>" width="80px;" height="80px;">
                                                        </td>
                                                    </tr>
                                                    <?php }?>
                                                    <tr>
                                                        <td style="padding-left:10px;">
                                                            <label for="tag-name">Site Name :
                                                                <input type="text" value="<?php echo esc_html($appdata['site_name']); ?>" name="site_name" style="width:275px;" required >
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-left:10px;">
                                                            <label for="tag-name">Site URL :
                                                                <input type="text" value="<?php echo esc_url($appdata['site_url']); ?>" name="site_url" style="width:275px;" required >
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding-left:10px;">
                                                            <label for="tag-name">Site Image URL :
                                                                <input type="text" value="<?php echo esc_url($appdata['site_image']); ?>" name="site_image" style="width:275px;">
                                                            </label>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        <?php } ?>
                                        <div>
                                            <input type="hidden" name="action" value="update_site_settings">
                                            <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                            <input type="submit" name="save-site-settings" id="save-site-settings" class="btn btn-primary" value="Save Site Settings">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- end  site info -->
                        <!--start profile settings-->
                        <div class="col-md-6 col-lg-6">
                            <div class="panel panel-default">
                                <div class="form-wrap box box-primary  box-body" style="width:50%;">
                                    <div class="panel-title">Profile Settings</div>
                                    <div class="panel-body">
                                        <form role="form" id="profile_form" method="post" action="admin.php?page=pushengage-admin&tab=gSettings">
                                            <div class="form-group">
                                                <label for="site_name">Name:</label>
                                                <input type="text" class="form-control" id="user_name" required maxlength="250" name="user_name" placeholder="Enter Name here"
                                                       value="<?php if(!empty($userdata['user_name'])) echo esc_html($userdata['user_name']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="site_url">Email:</label>
                                                <input type="text" disabled="" class="form-control" required maxlength="500" name="user_email"
                                                       value="<?php if(!empty($userdata['user_email'])) echo esc_html($userdata['user_email']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="timezones">Timezone:</label><br>
                                                <input type="hidden" name="site_id" value="20">
                                                <?php
                                                    if($timezone && isset($timezone)) {
                                                        $timezone = Pushengage::getIsoTimezone($timezone);
                                                    } else {
                                                        $timezone = "UTC";
                                                    }
                                                    $timezone_list = Pushengage::select_Timezone($timezone);
                                                    echo $timezone_list;
                                                ?>
                                            </div>
                                            <input type="hidden" name="action" value="update_profile">
                                            <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                            <div style="padding-top: 24px;">
                                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end  profile settings -->
                        <!-- Start wordpress post settings -->
                        <div class="col-md-6 col-lg-6">
                            <div class="panel panel-default">
                                <form id="wordpress_settings" method="post" action="admin.php?page=pushengage-admin&tab=gSettings" class="validate">
                                    <div class="form-wrap box box-primary  box-body">
                                        <div class="box-header with-border">
                                            <div class="panel-title">Wordpress Post Settings</div>
                                        </div>
                                        <div class="form-field form-required" style="max-width:100%">
                                            <label for="tag-name"><strong>Auto Push</strong></label>
                                            <input type="checkbox" name="pushengage-auto-push" value="1"
                                                   style="min-width:12px; min-height:12px; margin:4px;" <?php if(!empty($pushengage_settings['autoPush'])) checked($pushengage_settings['autoPush'], 1); ?>/>
                                            Automatically send a push notification to your subscribers every time you
                                            publish a new post.
                                        </div>
                                        <div class="form-field">
                                            <label for="tag-slug"><strong>Allow All Post Types</strong></label>
                                            <input type="checkbox" name="pushengage-all-post-types"
                                                   value="1" style="min-width:12px; min-height:12px; margin:4px;" <?php if(!empty($pushengage_settings['all_post_types'])) checked($pushengage_settings['all_post_types'], 1); ?> />
                                            Allow All Types of Posts  to automatically trigger a notification.
                                        </div>
                                        <div class="form-field term-parent-wrap">
                                            <label for="parent"><strong>Use Custom Images</strong></label>
                                            <input type="radio" name="pushengage-custom-image"
                                                   value="1" <?php if(!empty($pushengage_settings['use_featured_image'])) echo 'checked'; ?> style="margin : 4px" />
                                                   Use featured image from post as custom image for notification image.</br>
                                            <input type="radio" name="pushengage-custom-image"
                                                   value="0" <?php if(empty($pushengage_settings['use_featured_image'])) echo 'checked'; ?> style="margin : 4px" />
                                                   Use featured image from post as custom image for notification big image.
                                        </div>
                                        <div class="form-field term-parent-wrap">
                                            <label for="parent"><strong>Subscription Popup</strong></label>
                                            <input type="checkbox" name="disable_subscription_popup"
                                                   value="1" style="min-width:12px; min-height:12px;" <?php if(!empty($pushengage_settings['disable_subscription_popup'])) checked($pushengage_settings['disable_subscription_popup'], 1); ?>  style="margin:4px" />
                                            Disable the subscription popup on page load.
                                        </div>
                                        <div>
                                            <input type="hidden" name="action" value="update_wordpress_settings">
                                            <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                            <input type="hidden" name="action_settings" value="post">
                                            <input type="submit" name="save-wordpress-settings" id="save-wordpress-settings" class="btn btn-primary" value="Save Wordpress Settings">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- End wordpress post settings -->
                        <!-- Start utm settings -->
                        <div class="col-md-6 col-lg-6">
                            <div class="panel panel-default">
                                <div class="form-wrap box box-primary  box-body" style="width:50%;">
                                    <form id="utm_settings" method="post" action="admin.php?page=pushengage-admin&tab=gSettings" class="validate">
                                        <div class="box-header with-border">
                                            <div class="panel-title">UTM Settings</div>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="utmcheckbox" id="utmcheckbox"
                                                       onclick="displayPeUtmDiv()" style="min-width:12px; min-height:12px;" <?php if(!empty($pushengage_settings['utmcheckbox'])) checked($pushengage_settings['utmcheckbox'], 1); ?> >
                                                <strong>Add UTM Parameters</strong>
                                            </label>
                                        </div>
                                        <div class="utmdiv" style="display:block;" id="utmdiv">
                                            <div class="form-group">
                                                <label for="utm_source"><strong>UTM Source</strong></label>
                                                <input type="text" class="form-control" style="width:400px"
                                                       id="utm_source" required="true"
                                                       maxlength="80" name="utm_source"
                                                       placeholder="Enter UTM Source here"
                                                       value="<?php if (isset($pushengage_settings['utm_source'])) echo esc_html($pushengage_settings['utm_source']); else echo 'pushengage'; ?>">
                                                UTM Source limit 80 characters
                                            </div>
                                            <div class="form-group">
                                                <label for="utm_medium"><strong>UTM Medium</strong></label>
                                                <input type="text" class="form-control" style="width:400px"
                                                       id="utm_medium" required="true"
                                                       maxlength="80" name="utm_medium"
                                                       placeholder="Enter UTM Medium here"
                                                       value="<?php if (isset($pushengage_settings['utm_medium'])) echo esc_html($pushengage_settings['utm_medium']); else echo 'push_notification'; ?>">
                                                UTM Medium limit 80 characters
                                            </div>
                                            <div class="form-group">
                                                <label for="utm_campaign"><strong>UTM Campaign</strong></label>

                                                <input type="text" class="form-control" style="width:400px"
                                                       id="utm_campaign" required="true"
                                                       maxlength="80" name="utm_campaign"
                                                       placeholder="Enter Notification URL here"
                                                       value="<?php if (isset($pushengage_settings['utm_campaign'])) echo esc_html($pushengage_settings['utm_campaign']); else echo 'pushengage'; ?>">
                                                UTM Campaign limit 80 characters
                                            </div>
                                        </div>
                                        <div>
                                            <input type="hidden" name="action" value="update_wordpress_settings">
                                            <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                            <input type="hidden" name="action_settings" value="utm">
                                            <input type="submit" name="save-utm-settings" id="save-utm-settings" class="btn btn-primary" value="Save UTM Settings">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- End utm settings -->
                    </div>
                    <!--End General settings -->
                </div>

                <!-- Subscription Dialog box settings-->
                <div id="subDialogbox" class="<?php
                if ($pe_dialogbox_settings == "active") {
                    echo " tab-pane fade in active";
                } else {
                    echo "tab-pane fade";
                }
                ?>">
                    <div class="row">
                        <div class="col-md-12" style="background-color:#f5f5f5;">
                            <div class="row">
                                 <!-- Start subscription dialog box setting -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="panel panel-default">
                                        <div class="panel-title">Subscription Dialogbox Settings</div>
                                        <?php
                                        $site_subdomain_for_browser_popup = $appdata['site_subdomain'];

                                        if (!filter_var($appdata['site_url'], FILTER_VALIDATE_URL) === false) {
                                            $site_url_for_browser_popup = parse_url($appdata['site_url'], PHP_URL_HOST);
                                            $ssite_url_for_browser_popup = explode("www.", $site_url_for_browser_popup);
                                            if (isset($ssite_url_for_browser_popup[1])) {
                                                $site_url_for_browser_popup = $ssite_url_for_browser_popup[1];
                                            }
                                        } else {
                                            $site_url_for_browser_popup = $appdata['site_url'];
                                        }

                                        ?>
                                        <div class="panel-body">
                                            <form role="form" id="subscription-dailoguebox" method="post"
                                                  enctype="multipart/form-data">
                                                <div class="form-group ">
                                                    <label for="site_name">Site Type</label>
                                                    <div class="material-switch pull-right">
                                                        <span class="http-text">HTTP</span>
                                                        <input id="switch-site-type" name="switch-site-type" type="checkbox" checked />
                                                        <label for="switch-site-type" class="label-default"></label>
                                                        <span class="https-text">HTTPS</span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="site_name">Dialogbox Type</label>
                                                    <select name="optin_type" id="optin_type">
                                                        <option value="6" <?php if ($dialogbox_type == "6") echo "selected"; ?>>
                                                            Large Safari Style Box
                                                        </option>
                                                        <?php if ($sub_data['name'] != 'FREE') { ?>
                                                        <option value="8" <?php if ($dialogbox_type == "8") echo "selected"; ?>>
                                                            Large Safari Style with Segment
                                                        </option>
                                                        <?php } ?>
                                                        <option value="1" <?php if ($dialogbox_type == "1") echo "selected"; ?>>
                                                            Safari Style Box
                                                        </option>
                                                        <option value="3" <?php if ($dialogbox_type == "3") echo "selected"; ?>>
                                                            Bell
                                                        </option>
                                                        <option value="2" <?php if ($dialogbox_type == "2") echo "selected"; ?>>
                                                            Bottom Placed Bar
                                                        </option>
                                                        <option id="single-step-optin-dialogbox" value="4" <?php if ($dialogbox_type == "4" || $dialogbox_type == "5") echo "selected"; ?>>
                                                            Push Single Step Optin
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-group quick-install-box">
                                                    <label for="quick-install">Quick Install <i
                                                                class="fa fa-question-circle" aria-hidden="true"
                                                                style="vertical-align: top;" data-toggle="tooltip"
                                                                title="If you set Quick Install to yes, you will collect subscription at https://yourdomain.pushengage.com/, and to go live you need to do (1)  add the PushEngage javascript.  If you wan to collecting subscription at your domain, set it to No, But it will require you to (1)  save 2 extra files and (2) add the Pushengage javascript"
                                                                data-original-title=""></i>
                                                    </label>
                                                    <div class="material-switch pull-right">
                                                        <span class="http-text">No</span>
                                                        <input id="quick-install-switch" name="optin_sw_support"
                                                               type="checkbox" <?php if (!$quick_install) echo 'checked' ?>/>
                                                        <label for="quick-install-switch" class="quick-install"
                                                               id="quick-install-switch-label"></label>
                                                        <span class="https-text">Yes</span>
                                                    </div>
                                                </div>

                                                <div class="form-group large-safaripopup-withsegment">
                                                    <div id="error-message">You must choose two segments.</div>
                                                    <label for="segments">Choose Segments</label>
                                                    <select multiple name="segments[]" id="segments" size="4">
                                                        <?php
                                                        if (!empty($segments_data)) {
                                                            $segments_str = '';
                                                            foreach ($segments_data as $segment) {
                                                                if (is_array($optin_segments_https) && in_array($segment['segment_name'], $optin_segments_https)) {
                                                                    if ($segments_str == '')
                                                                        $segments_str = $segment['segment_name'];
                                                                    else
                                                                        $segments_str .= ',' . $segment['segment_name'];
                                                                }

                                                                if ($segment['segment_name']) {
                                                                    ?>
                                                                    <option value="<?php echo esc_html($segment['segment_name']); ?>" <?php if (is_array($optin_segments_https) && in_array($segment['segment_name'], $optin_segments_https)) echo "selected"; ?>><?php echo esc_html($segment['segment_name']); ?></option>
                                                                <?php }
                                                            }
                                                        } ?>
                                                    </select>
                                                </div>

                                                <div class="form-group ">
                                                    <label for="optin_delay">Optin Delay Time</label>
                                                    <input type="number" min="0" max="600" step="1" class=""
                                                           style="margin-left:10px;width: 120px;" id="optin_delay"
                                                           required maxlength="250" name="optin_delay"
                                                           value="0"
                                                           placeholder="Enter delay">&nbsp;Seconds
                                                </div>
                                                <div class="form-group" id='hide-thanku-https'>
                                                    <label for="optin_title" id="optin_title_label">Optin Title</label>
                                                    <input type="text" class="form-control" id="optin_title" required
                                                           maxlength="250" name="optin_title"
                                                           placeholder="Enter Option Title here" value="">
                                                </div>
                                                <div class="form-group dialogbox-property">
                                                    <label for="optin_allow_btn_txt">Optin Allow Button Text</label>
                                                    <input type="text" class="form-control" id="optin_allow_btn_txt"
                                                           required maxlength="500" name="optin_allow_btn_txt"
                                                           placeholder="Enter Option Allow Button Text here" value="">
                                                </div>
                                                <div class="form-group dialogbox-property">
                                                    <label for="optin_close_btn_txt">Optin Close Button Text</label>
                                                    <input type="text" class="form-control" id="optin_close_btn_txt"
                                                           required maxlength="500" name="optin_close_btn_txt"
                                                           placeholder="Enter Option Close Button here" value="">
                                                </div>
                                                <input type="hidden" name="action" value="update_optin_settings">
                                                <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                                <input type="hidden" name="site_id" value="<?php if(!empty($site_id)) echo esc_html($site_id); ?>">
                                                <input type="hidden" name="site_type" id="site_type" value="0">
                                                <input type="hidden" name="set_site_type" id="set_site_type"
                                                       value="<?php if ($set_site_type == 'https') echo 'https'; else  echo 'http'; ?>">
                                                <div>
                                                    <button type="submit" class="btn btn-primary  upd_opt_set">
                                                        Update Optin Settings
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End subscription dialog box setting -->
                                <!-- Start subscription dialog preview -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="panel panel-default">
                                        <div class="panel-title">
                                            <div>Preview</div>
                                        </div>
                                        <div id="handle-step-1">
                                            <span class="popup-step" id="popup-1">1</span>
                                            <span class="step-1-text">Please see Below</span>
                                        </div>
                                        <div class="panel-body https-native-popup">
                                            <div class="col-md-6">
                                                <div id="right_workspace" style="margin-top:10px"></div>
                                                <div id="second-sub-popup">
                                                    <span class="popup-step step-3" id="popup-2">2</span>
                                                    <div class='alert-browser-notification-popup arrow_box' id="default-sub-popup">
                                                        <span class='notification-content-close'>&#10006;</span>
                                                        <p class='alert-browser-notification-popup-url'>
                                                            <?php echo esc_url($site_url_for_browser_popup); ?> want to:
                                                        </p>
                                                        <p class='alert-browser-notification-popup-show'>
                                                            <img src="<?php echo PUSHENGAGE_PLUGIN_URL; ?>/images/bell.png" style='width:18px;margin: 0 8px 0 23px;height: 15px;'></img>
                                                            Show notifications
                                                        </p>
                                                        <p style='text-align:right'>
                                                            <a class='notification-allow'>Allow</a>
                                                            <a class='notification-close'>Block</a>
                                                        </p>
                                                    </div>

                                                    <div class='pushengagesweet-alert-optin-4 showpushengagesweetAlert visible' id="http-single-step-optin">
                                                        <div class='pushengagesweet-alert-optin-4-content'>
                                                            <h2 id='_pe_optin_settings_optin_title' class='http-single-step-optin-title'></h2>
                                                            <p class='pushengagesweet-alert-optin-4-poweredby'>powered by PushEngage.com</p>
                                                        </div>
                                                        <div class='sa-button-container'>
                                                            <div class='sa-confirm-button-container'>
                                                                <button class='confirm'>CLOSE</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End subscription dialog preview -->
                            </div>

                            <div class="row" id='intermediate-page-hide'>
                                <!-- Start intermediate page setting -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="panel panel-default">
                                        <div class="panel-title">Intermediate Page Settings</div>
                                        <div class="panel-body">
                                            <form role="form" id="intermediate_page_settings_form" method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="site_name">Page Heading</label>
                                                    <input type="text" class="form-control" id="page_heading" required
                                                           maxlength="250" name="page_heading"
                                                           placeholder="Enter Option Title here"
                                                           value="<?php echo $intermediate_page_heading; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="site_name">Tag Line</label>
                                                    <input type="text" class="form-control" id="page_tagline" required
                                                           maxlength="250" name="page_tagline"
                                                           placeholder="Enter Option Title here"
                                                           value="<?php echo $intermediate_page_tagline; ?>">
                                                </div>

                                                <input type="hidden" name="action" value="update_optin_intermediate_page_settings">
                                                <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                                <input type="hidden" name="site_id" value="<?php if(!empty($site_id)) echo esc_html($site_id); ?>">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">
                                                        Update Page Settings
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End intermediate page setting -->
                                <!-- Start intermediate page preview -->
                                <div class="col-md-6 col-lg-6">
                                    <span class="popup-step step-3" id="popup-3">2</span>
                                    <div class="col-md-10 col-lg-10 browser-mockup" style="background-color: #fff;text-align: center;padding: 35px;">
                                        <div class="alert-browser-notification-popup arrow_box">
                                            <span class="notification-content-close">✖</span>
                                            <p class="alert-browser-notification-popup-url">
                                                <?php echo esc_url($site_subdomain_for_browser_popup) . "." ?> pushengage.com want to:
                                            </p>
                                            <p class="alert-browser-notification-popup-show">
                                                <img src="<?php echo PUSHENGAGE_PLUGIN_URL . 'images/bell.png'; ?>" style="width:18px;margin: 0 8px 0 23px;height: 15px;">
                                                Show notifications
                                            </p>
                                            <p style="text-align:right">
                                                <a class="notification-allow">
                                                    Allow
                                                </a>
                                                <a class="notification-close">
                                                    Block
                                                </a>
                                            </p>
                                        </div>
                                        <img src="<?php echo esc_url($appdata['site_image']); ?>" style="max-width: 80px;">
                                        <p style="padding-top: 14px;">
                                            <b id="page_heading_view"><?php echo $intermediate_page_heading; ?></b>
                                        </p>
                                        <p>
                                            <small id="page_tagline_view"><?php echo $intermediate_page_tagline; ?></small>
                                        </p>
                                    </div>
                                </div>
                                <!-- End intermediate page preview -->
                            </div>
                        </div>
                    </div>
                    <!--End of Subscription Dialogbox setting-->
                </div>

                <!--Start welcome notification settings-->
                <div id="welcome_notification" class="<?php
                if ($pe_welcome_noti_settings == "active") {
                    echo " tab-pane fade in active";
                } else {
                    echo "tab-pane fade";
                }
                ?>">
                    <div class="row">
                        <div class="col-md-8 col-lg-8">
                            <div class="panel panel-default">
                                <div class="panel-title">Welcome Notification</div>
                                <div class="panel-body">
                                    <form role="form" method="post" enctype="multipart/form-data" action="admin.php?page=pushengage-admin&tab=welcome_notification">
                                        <div class="form-group">
                                            <label for="notification_title">Notification Title</label>
                                            <input type="text" class="form-control" id="notification_title"
                                                   value="<?php echo ($welcome_note_data->notification_title) ? esc_html($welcome_note_data->notification_title) : ""; ?>"
                                                   required maxlength="85"
                                                   name="notification_title" value="test"
                                                   placeholder="Enter Notification Title here">
                                            Notification title limit 85 characters
                                        </div>
                                        <div class="form-group">
                                            <label for="notification_message">Notification Message</label>
                                            <input type="text" class="form-control" id="notification_message"
                                                   value="<?php echo ($welcome_note_data->notification_message) ? esc_html($welcome_note_data->notification_message) : ""; ?>"
                                                   required maxlength="135"

                                                   name="notification_message"
                                                   placeholder="Enter Notification Message here">
                                            Notification message limit 135 characters
                                        </div>
                                        <div class="form-group">
                                            <label for="notification_message">Notification URL</label>
                                            <input type="url" class="form-control" id="display_notification_url"
                                                   required maxlength="1600"
                                                   value="<?php echo ($welcome_note_data->notification_url) ? esc_url($welcome_note_data->notification_url) : ""; ?>"
                                                   name="display_notification_url"
                                                   placeholder="Enter Notification URL here">
                                            <input type="hidden" name="notification_url" id="notification_url">
                                            Example Notification URL : http://www.pushengage.com
                                        </div>
                                        <input type="hidden" name="welcome_enabled" value="false">
                                        <div class="checkbox checkbox-primary">
                                            <input name="welcome_enabled" value="true"
                                                   type="checkbox" <?php if ($welcome_note_data->welcome_enabled == "true" || $welcome_note_data->welcome_enabled == "1" ) echo "checked='checked'"; ?>
                                                   style="margin-left:0px !important; min-width:12px; min-height:12px;">

                                            <label for="utmcheckbox">
                                                Send Welcome Notifications to Subscribers
                                            </label>

                                        </div>
                                        <input type="hidden" name="action" value="save_welcome_notification">
                                        <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                        <div class="panel-footer">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End of welcome notification settings-->

                <!--Start segmentation settings-->
                <div id="automatic_segmentation" class="<?php
                if ( "active" === $pe_segment_settings ) {
                    echo " tab-pane fade in active";
                } else {
                    echo "tab-pane fade";
                }
                ?>">

                    <!-- Automatic segmentation delete modal -->
                    <div class="modal fade" id="pe-delete-automatic-segment-modal" role="dialog">
                      <div class="modal-dialog modal-md" style="width: fit-content;">
                        <div class="modal-content">
                              <div class="modal-body">
                                <p style="font-size: 16px; color: #333333;">Are you sure, you want to delete this segment ?</p>
                              </div>
                              <div class="modal-footer" style="border-top: unset; padding-top: unset;">
                                <button type="submit" class="btn btn-danger" data-segment-id="" id="pe-confirm-delete-auto-segment" onclick="deletePeAutoSegment(this, true)">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              </div>
                        </div>
                      </div>
                    </div>
                    <!-- End automatic segmentation delete modal -->

                    <!-- Automatic segmentation edit modal -->
                    <div class="modal fade" id="pe-edit-automatic-segment-modal" role="dialog">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Edit Automatic Segment</h4>
                          </div>
                          <form method="post">
                              <div class="modal-body">
                                <label>Segment Name :</label>
                                <br/>
                                <input type="text" name="pe_segment_name" id="pe-edit-segment_name" style="width: 100%" required/>
                                <br/><br/>
                                <input type="checkbox" name="pe_segment_page_visit" id="pe_edit_segment_page_visit" value="1" style="width: 12px;height:12px;" />
                                <label for="pe_edit_segment_page_visit" >&nbsp;&nbsp;Segment on Page Visit</label>&nbsp;(Optional)
                                <br/><br/>
                                <p><label>Specify Your Segment Addition Pattern</label></p>
                                <label>Include Patterns :</label>
                                <div id="pe_edit_include_pattern" >
                                  <!-- append the include segment HTMl containers by jquery -->
                                </div>
                                <br/>
                                <label>Exclude Patterns :</label>
                                <div id="pe_edit_exclude_pattern" >
                                  <!-- append the exlcude segmens HTML containers by jquery -->
                                </div>
                              </div>
                              <div class="modal-footer">
                                <input type="hidden" name="pe_include_segment_count" id="pe_edit_include_segment_count"  value="1">
                                <input type="hidden" name="pe_exclude_segment_count" id="pe_edit_exclude_segment_count" value="1">
                                <input type="hidden" name="action" value="update_automatic_segmentation">
                                <input type="hidden" name="pe_segment_id"  id="pe_segment_id" />
                                <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                <button type="submit" class="btn btn-primary" id="update_automatic_segmentation" >Update</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                              </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <!-- End automatic segmentation edit modal -->

                    <!-- Automatic segmentation create modal -->
                    <div class="modal fade" id="pe-create-automatic-segment-modal" role="dialog">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Create Automatic Segment</h4>
                          </div>
                            <form method="post">
                                <div class="modal-body">
                                  <label>Segment Name :</label>
                                  <br/>
                                  <input type="text" name="pe_segment_name" id="pe-create-segment_name" style="width: 100%" required/>
                                  <br/><br/>
                                  <input type="checkbox" name="pe_segment_page_visit" id="pe_segment_page_visit" value="1" style="width: 12px;height:12px;" />
                                  <label for="pe_segment_page_visit" >&nbsp;&nbsp;Segment on Page Visit</label>&nbsp;(Optional)
                                  <br/><br/>
                                  <p><label>Specify Your Segment Addition Pattern</label></p>
                                  <label>Include Patterns :</label>
                                  <div id="pe_include_pattern" >
                                    <div id="pe_include_pattern_container_1">
                                        <select name="pe_include_pattern_rule_options_1" value="start" >
                                            <option value="start" >Start With</option>
                                            <option value="contains" >Contains</option>
                                            <option value="exact" >Exact Match</option>
                                        </select>
                                        <input type="text" name="pe_include_pattern_value_1" id="pe_include_pattern_value_1" style="width:60%" />
                                        <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_include_pattern_1" onClick="addPeIncludeSegmentContainer(this)"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        <button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_1" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                  </div>
                                  <br/>
                                  <label>Exclude Patterns :</label>
                                  <div id="pe_exclude_pattern" >
                                    <div id="pe_exclude_pattern_container_1">
                                        <select name="pe_exclude_pattern_rule_options_1" value="start" >
                                            <option value="start" >Start With</option>
                                            <option value="contains" >Contains</option>
                                            <option value="exact" >Exact Match</option>
                                        </select>
                                        <input type="text" name="pe_exclude_pattern_value_1" id="pe_exclude_pattern_value_1" style="width:60%" />
                                        <button type="button" class="btn btn-success" id="pe_add_exclude_pattern_1" onClick="addPeExcludeSegmentContainer(this)" > <i class="fa fa-plus" aria-hidden="true"></i></button>
                                        <button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_1" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <input type="hidden" name="pe_include_segment_count" id="pe_include_segment_count"  value="1">
                                  <input type="hidden" name="pe_exclude_segment_count" id="pe_exclude_segment_count" value="1">
                                  <input type="hidden" name="action" value="create_automatic_segmentation">
                                  <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce('pe_token'); ?>"/>
                                  <button type="submit" class="btn btn-primary" id="create_automatic_segmentation" >Create</button>
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                    <?php
                           // blocking automation feature for free users.
                           if( 1 == $pe_session['check_auth']['subscription_plan_id'] ) { ?>
                                <div class="kode-alert kode-alert-icon alert6-light" style="height: 240px;" >
                                    <p style="text-align:center"><i class="fa fa-info" style="position: unset; display: inline-block;"></i> Automatic Segmentation is not available for Free Plan.  Please upgrade your plan or contact care@pushengage to know more.</p>
                                    <div style="text-align:center;margin-top: 80px;">
                                        <a href="https://app.pushengage.com/account/billing-subscription?drawer=true" target="_blank" ><button class="btn btn-primary btn-responsive" >Upgrade Your Plan</button></a>
                                    </div>
                                </div>
                    <?php   }
                            else { ?>
                                <div class="col-md-8 col-lg-8">
                                    <div class="panel panel-default">
                                        <div class="panel-title">Configure Automatic Segmentation</div>
                                        <div class="panel-body">
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pe-create-automatic-segment-modal" >Create URL Pattern to Auto Segment</button>
                                            <table class="table table-striped">
                                                <thead>
                                                  <tr>
                                                    <th class="col-xs-4" style="padding:15px">Segment Name</th>
                                                    <th class="col-xs-5.5" style="padding:15px">Segment Criteria</th>
                                                    <th class="col-xs-2.5" style="padding:15px">Actions</th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                <?php

                                                foreach($automatic_segmentation_data as $automatic_segment) {
                                                    $include_start = array();
                                                    $include_contains = array();
                                                    $include_exact = array();
                                                    $exclude_start = array();
                                                    $exclude_contains = array();
                                                    $exclude_exact = array();

                                                    if(isset($automatic_segment['segment_criteria']['include']) && !empty($automatic_segment['segment_criteria']['include'])) {
                                                        foreach($automatic_segment['segment_criteria']['include'] as $include_rules) {
                                                            switch($include_rules['rule']) {
                                                                case 'start':
                                                                    array_push($include_start, $include_rules['value']);
                                                                    break;
                                                                case 'contains';
                                                                    array_push($include_contains, $include_rules['value']);
                                                                    break;
                                                                case 'exact':
                                                                    array_push($include_exact, $include_rules['value']);
                                                                    break;
                                                                default:
                                                                    //do nothing
                                                            }
                                                        }
                                                    }
                                                    if(isset($automatic_segment['segment_criteria']['exclude']) && !empty($automatic_segment['segment_criteria']['exclude'])) {
                                                        foreach($automatic_segment['segment_criteria']['exclude'] as $exclude_rules) {
                                                            switch($exclude_rules['rule']) {
                                                                case 'start':
                                                                    array_push($exclude_start, $exclude_rules['value']);
                                                                    break;
                                                                case 'contains';
                                                                    array_push($exclude_contains, $exclude_rules['value']);
                                                                    break;
                                                                case 'exact':
                                                                    array_push($exclude_exact, $exclude_rules['value']);
                                                                    break;
                                                                default:
                                                                    //do nothing
                                                            }
                                                        }
                                                    }

                                                    echo '<tr>
                                                            <td>'.esc_html($automatic_segment['segment_name']).'</td>
                                                            <td>';
                                                                if(!empty($include_start) || !empty($include_contains) || !empty($include_exact)) {
                                                                    echo '<b>Include URL Pattern - </b>';
                                                                    if(!empty($include_start)){
                                                                        echo "<br><b>start : </b>";
                                                                        foreach($include_start as $key => $value) {
                                                                            if(strlen($value) > 40) {
                                                                                $value = substr(esc_html($value), 0, 40).'...';
                                                                            }

                                                                            $key++;
                                                                            echo '<br>('.$key.') '.$value;
                                                                        }
                                                                    }
                                                                    if(!empty($include_contains)){
                                                                        echo "<br><b>contains : </b>";
                                                                        foreach($include_contains as $key => $value) {
                                                                            if(strlen($value) > 40) {
                                                                                $value = substr(esc_html($value), 0, 40).'...';
                                                                            }

                                                                            $key++;
                                                                            echo '<br>('.$key.') '.$value;
                                                                        }
                                                                    }
                                                                    if(!empty($include_exact)) {
                                                                        echo "<br><b>exact : </b>";
                                                                        foreach($include_exact as $key => $value) {
                                                                            if(strlen($value) > 40) {
                                                                                $value = substr(esc_html($value), 0, 40).'...';
                                                                            }

                                                                            $key++;
                                                                            echo '<br>('.$key.') '.$value;
                                                                        }
                                                                    }
                                                                }

                                                                if(!empty($exclude_start) || !empty($exclude_contains) || !empty($exclude_exact)) {
                                                                    echo '<br><b>Exclude URL Pattern - </b>';
                                                                    if(!empty($exclude_start)) {
                                                                        echo "<br><b>start : </b> ";
                                                                        foreach($exclude_start as $key => $value) {
                                                                            if(strlen($value) > 40) {
                                                                                $value = substr(esc_html($value), 0, 40).'...';
                                                                            }

                                                                            $key++;
                                                                            echo '<br>('.$key.') '.$value;
                                                                        }
                                                                    }
                                                                    if(!empty($exclude_contains)) {
                                                                        echo "<br><b>contains : </b> ";
                                                                        foreach($exclude_contains as $key => $value) {
                                                                            if(strlen($value) > 40) {
                                                                                $value = substr(esc_html($value), 0, 40).'...';
                                                                            }

                                                                            $key++;
                                                                            echo '<br>('.$key.') '.$value;
                                                                        }
                                                                    }
                                                                    if(!empty($exclude_exact)) {
                                                                        echo "<br><b>exact : </b>";
                                                                        foreach($exclude_exact as $key => $value){
                                                                            if(strlen($value) > 40) {
                                                                                $value = substr(esc_html($value), 0, 40).'...';
                                                                            }

                                                                            $key++;
                                                                            echo '<br>('.$key.') '.$value;
                                                                        }
                                                                    }
                                                                }
                                                    echo '</td>
                                                            <td>
                                                                <button class="btn btn-success" onclick="editPeAutoSegment(this)" data-segment-id="'.$automatic_segment['segment_id'].'" ><i class="fa fa-pencil" aria-hidden="true"  ></i></button>
                                                                <button class="btn btn-danger" data-segment-id="'.$automatic_segment['segment_id'].'" onclick="deletePeAutoSegment(this, false)"  data-toggle="modal" data-target="#pe-delete-automatic-segment-modal"  ><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                            </td>
                                                          </tr>';
                                                     } ?>
                                                </tbody>
                                            </table>
                                            <?php
                                                // show an empty div block screen, if there is no any automatic segment.
                                                if( empty($automatic_segmentation_data )) {
                                            ?>
                                                    <div class="empty-div-placeholder">No Automatic Segment Found</div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4">
                                    <div class="panel panel-default">
                                        <div class="panel-title">Test Your Auto Segment Rules</div>
                                        <div class="panel-body">
                                            <div class="alert alert-info show" style="margin-left: unset;">
                                                <i class="fa fa-info" aria-hidden="true"></i> &nbsp;&nbsp; URL will be tested against all Segment's Rules
                                            </div>
                                            <p style="margin-top: 8px;"><label>URL to Test :</label></p>
                                                <input type="text" name="pe_segment_test_url" id="pe_segment_test_url"  placeholder="Enter url to test against all segments" style="width: 100%" required>
                                                <button type="submit" class="btn btn-primary"  id="pe-test-auto-segment" style = "margin-top: 10px;">Test</button>
                                            <div id="pe-matched-auto-segment">
                                                <!-- show the matached segments -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    <?php   } ?>

                    </div>
                </div>
                <!--End of segmentation settings-->
                <!--Start category segmentation settings-->
                <div id="category_segmentation" class="<?php
                if ( "active" === $pe_category_segment_settings ) {
                    echo " tab-pane fade in active";
                } else {
                    echo "tab-pane fade";
                }
                ?>">
                    <div class="row">
                    <?php
                            // blocking automation feature for free users.
                            if( 1 == $pe_session['check_auth']['subscription_plan_id'] ) { ?>
                                <div class="kode-alert kode-alert-icon alert6-light" style="height: 240px">
                                    <p style="text-align:center"><i class="fa fa-info" style="position: unset;display: inline-block;"></i> Category Segmentation is not available for Free Plan.  Please upgrade your plan or contact care@pushengage to know more.</p>
                                    <div style="text-align:center;margin-top: 80px;">
                                        <a href="https://app.pushengage.com/account/billing-subscription?drawer=true" target="_blank" ><button class="btn btn-primary btn-responsive" >Upgrade Your Plan</button></a>
                                    </div>
		                        </div>
                    <?php   } else { ?>

                                <!-- create segment modal -->
                                <div class="modal fade" id="pe-create-segment-modal" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Create A New Segment</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    You can create a pushengage segment, also you can check the number of subscribers belong to that segment from your pushengage <a href="https://app.pushengage.com/segmentation" target="_blank" >dashboard</a>.
                                                </p>
                                                <label>Segment Name :</label>
                                                <br/>
                                                <input type="text" name="pe_segment_name" id="pe-custom-segment-name" style="width: 100%" required/>
                                                <div id="pe-error-create-segment">
                                                <!-- append error message here -->
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" id="create-pushengage-segment" >Create</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End custom segment modal -->

                                <div class="col-md-10 col-lg-10">
                                    <div class="panel panel-default">
                                        <div class="panel-title">Configure Automatic Category Segmentation</div>
                                        <div class="panel-body">
                                            <form role="form" method="post" action="admin.php?page=pushengage-admin&tab=category-segmentation">
                                                <div class="row" style="padding-bottom: 28px"> 
                                                    <div class="alert alert-info show" style="margin-left:unset; margin-top:8px; margin-bottom:40px;">
                                                        <i class="fa fa-info" aria-hidden="true"></i> 
                                                        &nbsp;&nbsp; Category segmentation is used to add the visitor into a pushengage segment when they visit a particular post's category.
                                                    </div>
                                                    <div class="col-md-8"> 
                                                        <div class="form-field form-required" style="max-width:100%;">
                                                            <p style="font-weight: 200; font-size: 14px; display: inline; margin-right: 16px;">
                                                                Enable Category Segmentation
                                                            </p>
                                                            <div class="material-switch">
                                                                <input id="enable-category-segmentation" name="category_segmentation_enabled" type="checkbox" value="1" <?php if($category_segmentation_enabled) { echo "checked"; } ?> />
                                                                <label for="enable-category-segmentation" class="label-default"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-right category-segmentation-add-new-segment">
                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pe-create-segment-modal"  style="display:inline-block; line-height:1.0" > Add a new segment</button>
                                                    </div>
                                                </div>
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="col-xs-4.5" style="padding:15px">Wordpress Category Name</th>
                                                            <th class="col-xs-4.5" style="padding:15px">Pushengage Segment Name</th>
                                                            <th class="col-xs-3 text-center" style="padding:15px">Enable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            // Generate table row  on the basis of wordpress category list.
                                                            foreach ( $wp_category_list as $key => $wp_category ) { 

                                                                // these $category_segment_checkbox and $selected_segment variables are used to make saved segment selected in the option list 
                                                                // and to mark the checkbox checked for the saved checkbox against the selected segment.
                                                                $category_segment_checkbox  = false;
                                                                $selected_segment           = '';
                                                                
                                                                foreach ( $wp_category_pe_segment_map as $wp_category_pe_segment ) { 

                                                                    if ( $wp_category_pe_segment->category_name === $wp_category->cat_name && !empty( $wp_category_pe_segment->segment_name ) ) {
                                                                        $category_segment_checkbox  = false;
                                                                        $selected_segment           = $wp_category_pe_segment->segment_name;

                                                                        if ( 1 === $wp_category_pe_segment->status ) {
                                                                            $category_segment_checkbox = true; 
                                                                        }
                                                                    }
                                                                } 
                                                        ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php echo $wp_category->cat_name; ?>
                                                                        <input type="hidden" name="<?php echo "category_name_".$key; ?>" value="<?php echo $wp_category->cat_name; ?>" >
                                                                    </td>
                                                                    <td>
                                                                        <select style="width: 80%" class="select-pe-segment" id='<?php echo "segment_name_".$key; ?>' name='<?php echo "segment_name_".$key; ?>'>
                                                                            <option>
                                                                                <?php
                                                                                    if ( empty( $pe_segment_list ) ) {
                                                                                        echo "No segment found";
                                                                                    } else {
                                                                                        echo "Select a segment";
                                                                                    }
                                                                                ?>
                                                                                
                                                                            </option>;
                                                                            <?php
                                                                                // generate "option" tag on the basis of pushengage custom segments.
                                                                                foreach ( $pe_segment_list as $pe_segment ) {
                                                                            ?>
                                                                                    <option <?php if ( $selected_segment === $pe_segment["segment_name"]) { echo "selected"; } ?> >
                                                                                        <?php echo $pe_segment["segment_name"]; ?>
                                                                                    </option>
                                                                            <?php
                                                                                } 
                                                                            ?>
                                                                        </select>
                                                                        <div id="<?php echo "segment_name_".$key."_error"; ?>">
                                                                        <!-- append error message here -->
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <input class="large-checkbox pe-category-segmentation-checkbox" id='<?php echo "segment_checkbox_".$key; ?>' name='<?php echo "segment_checkbox_".$key; ?>' type="checkbox"  <?php if($category_segment_checkbox) { echo 'checked'; }?> value="1">
                                                                    </td>
                                                                </tr>
                                                    <?php  
                                                            }
                                                    ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                                    // show an empty div block screen, if there is no any category in the wordpress post.
                                                    if ( empty( $wp_category_list ) ) {
                                                ?>
                                                        <div class="empty-div-placeholder">No Category Found</div>
                                                <?php
                                                    }
                                                ?>
                                                <div class="panel-footer">
                                                    <input type="hidden" name="pe_token" value="<?php echo wp_create_nonce( 'pe_token' ); ?>"/>
                                                    <input type="hidden" name="action" value="update_wordpress_settings">
                                                    <input type="hidden" name="action_settings" value="update_category_segmentation_settings">
                                                    <button type="submit" class="btn btn-primary" id="pe-update-category-segment-settings" >Update Category Segment Settings</button>
                                                    <p style="margin-top: 16px;margin-bottom: 16px;">
                                                        NOTE : If you are using any caching tool, then you need to clear the cache after updating the category segmentation settings.
                                                    </p>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                    <?php   } ?>

                </div>
                <!--End of category segmentation settings-->
                </div>
            </div>
        </div>
        <?php include_once('footer.php'); ?>
    </div>

<?php

      // all PHP dyanamic variable javascripts.
      include_once('admin-scripts.php');
}


?>

<!-- Some common/global javascript functions -->
<script>

    // sending debug data to the server on button click.
    function peSendDataToServer() {
      $('#pe-send-debug-data-btn').html('<i class="fa fa-spinner fa-spin"></i>Sending dubugging data to server')

       <?php
         $pushengage_settings = Pushengage::pushengage_settings();
         unset($pushengage_settings['appKey']);
       ?>
        var getPushengageSettings = '<?php echo json_encode($pushengage_settings) ?>';
        var wordpressCurrentVersion = '<?php echo get_bloginfo('version'); ?>';
        var options = {
           method:"POST",
           mode:"no-cors",
           body:JSON.stringify({
              "name" : "wordpressDebuggingData",
              "app" : "wordpressPlugin",
              "version" : wordpressCurrentVersion,
              "data" : getPushengageSettings
           }),
           headers: {
              "Content-Type":"application/json"
           }
        };

        fetch('https://notify.pushengage.com/v1/logs',options).then(function(e) {
            $('#pe-send-debug-data-btn').html('Send dubugging data to server')
            $('#pe-troubleshoot-modal').modal('hide')
        })
    }

</script>
