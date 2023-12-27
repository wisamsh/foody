<?php
    wp_enqueue_style( 'bootstrap', PUSHENGAGE_URL . 'css/bootstrap.css', array(), "", "all" );
    wp_enqueue_style( 'style', PUSHENGAGE_URL . 'css/style.css', array(), "", "all" );
    wp_enqueue_style( 'shortcuts', PUSHENGAGE_URL . 'css/shortcuts.css', array(), "", "all" );
    wp_enqueue_style( 'responsive', PUSHENGAGE_URL . 'css/responsive.css', array(), "", "all" );
    wp_enqueue_style( 'font-awesome.min', PUSHENGAGE_URL . 'css/font-awesome.min.css', array(), "", "all" );
    wp_enqueue_style( 'style1', PUSHENGAGE_URL . 'css/pe-style.css', array(), "", "all" );
    wp_enqueue_style('pe-admin-style', PUSHENGAGE_URL . 'css/pe-admin.css', array(), "", "all");
    wp_enqueue_script( 'pe-bootstrap-min-js', PUSHENGAGE_URL . 'js/bootstrap-min.js', array(), "", "all" );

    if(!empty($pe_session['tabdata']['site_info'])) {
        $appdata = $pe_session['tabdata']['site_info'];
    }

    if(!empty($pe_session['tabdata']['subscription_plans'])) {
        $sub_data = $pe_session['tabdata']['subscription_plans'];
    }

    if(!empty($pe_session['tabdata']['active_subscribers'])) {
        $active_subscriber = json_decode($pe_session['tabdata']['active_subscribers']);
        $active_subscriber = $active_subscriber->data->count;
        $pe_session['active_subscriber'] = $active_subscriber;
    }

?>

<script type="text/javascript">var $ = jQuery.noConflict();</script>
<div id="toggle-dashboard-link"><a href="https://app.pushengage.com" target="_blank">
    <i class="fa fa-home toggle-icon" aria-hidden="true"></i><span>Goto PushEngage</span></a>
</div>

<div class="row plan-subscriber-main-header">
    <div class="col-md-12 content-header" style="">
        <div class="pe-logo">
            <span>PushEngage</span>
        </div>

        <div class="pe-site-div" style="width: auto;">
        <?php
            $date1= new DateTime($appdata['expiry_date']);
            $date2= new DateTime(date("Y-m-d"));
            $diff=date_diff($date1,$date2);
            $daysleft=$diff->days;
            $trial_text = " | <span style='color:blue'>Trial period will expire in $daysleft days</span>";
        ?>
        <p>&nbsp;Plan : <?php echo !empty($sub_data['name'])?$sub_data['name']:'';?> <span style="color:blue">|</span>  Active Subscribers (<?php echo !empty($active_subscriber)?esc_html($active_subscriber):0;?>) / Limit (<?php echo !empty($sub_data['subscribers_limit'])?esc_html($sub_data['subscribers_limit']):'';?>)<?php if(!empty($appdata['is_trial'])) echo $trial_text;?> <span style="color:blue">|</span> Site Name : <?php echo !empty($appdata['site_name'])?esc_html($appdata['site_name']):'';?>&nbsp;</p>
      </div>
    </div>
</div>

<script>
// override default title given by wordpress to our custom title.
document.title = 'PushEngage';

$('#toggle-dashboard-link').hover(function() {
    $(this).animate({
        right: 0
    }, "slow");
});

$('#toggle-dashboard-link').mouseleave(function() {
    $(this).animate({
        right: "-117px"
    }, "slow");
});

</script>
