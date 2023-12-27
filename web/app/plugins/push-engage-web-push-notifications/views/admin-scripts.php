<script>

// this is for the displaying and hiding  UTM input fields div block in case of checking and unchecking the UTM checkbox.
if (document.getElementById("utmcheckbox") != undefined && !document.getElementById("utmcheckbox").checked) {
    document.getElementById("utm_source").required = false;
    document.getElementById("utm_medium").required = false;
    document.getElementById("utm_campaign").required = false;
}

function displayPeUtmDiv() {
    if (document.getElementById("utmcheckbox").checked) {
        document.getElementById("utm_source").required = true;
        document.getElementById("utm_medium").required = true;
        document.getElementById("utm_campaign").required = true;
    }
    else {
        document.getElementById("utm_source").required = false;
        document.getElementById("utm_medium").required = false;
        document.getElementById("utm_campaign").required = false;
    }
};

// if optin_type = 8, then, show the option to select segments in the form.
$('#optin_type').change(function () {
    if ($('#optin_type').val() == 8)
        $('.large-safaripopup-withsegment').show();
    else
        $('.large-safaripopup-withsegment').hide();
});


$(document).ready(function () {
    // on clicking the submit button,
    // if user submits form with selecting less than two or more than two segments then show error message.
    $(document).on("click", '.upd_opt_set', function (e) {

        // check if optin type is 8(i.e, safari style with segments), then only proceed.
        if($('#optin_type').val() != 8) {
            return;
        }

        var segments_count = $('#segments').val().length;
        if (segments_count < 2) {
            $('#error-message').show();
            $('#segments').focus();
            return false;
        }
        else {
            $('#error-message').hide();
            return true;
        }
    });

    // on selecting the segment, check where to update either segment-1 or segment-2 in the UI.
    pe_segment_1 = "";
    pe_segment_2 = "";
    $(document).on("change", '#segments', function (e) {
        var seg_names = $("#segments").val();
        if (seg_names != undefined && seg_names != null && seg_names.length > 0) {
            if(document.getElementById("pe_segment_1").checked) {
                pe_segment_1 = seg_names[0];
                $('.lbl_segment1').html(seg_names[0]);
            }
            if(document.getElementById("pe_segment_2").checked) {
                pe_segment_2 = seg_names[0];
                $('.lbl_segment2').html(seg_names[0]);
            }
            if(pe_segment_1 && pe_segment_2) {
                $("#segments").val([pe_segment_1, pe_segment_2]);
            }
        }
        else {
            $('.lbl_segment1').html('Segment1');
            $('.lbl_segment2').html('Segment2');
        }
    })
})

function hideLargeSafariBoxExtraInput() {
    $("#optin-message-box :input, #optin-allow-button-footer-box :input, #optin-close-button-footer-box :input, #optin-img-url-box :input, #optin-footer-txt-box :input").prop('required', null);
    $('#optin-message-box,#optin-allow-button-footer-box,#optin-close-button-footer,#optin-img-url-box,#optin-footer-txt-box,#optin-img-enable-box,#optin-bg-box,#optin-close-button-footer-box').hide();
}


// handling to hide or show intermediate page as a 2nd step of subsription.
// Also, handling hide and show of seconf native pop up, in case of Quick Install ON.
$(document).on('click', '#quick-install-switch-label', function () {
    var quickInstallSwitchValue = $('#quick-install-switch').is(":checked");

    if (quickInstallSwitchValue) {
        $('#intermediate-page-hide').hide();
        $('#second-sub-popup').show();
    }
    else {
        $('#intermediate-page-hide').show();
        $('#second-sub-popup').hide();
    }
});

// it is replacing the title field parallelly
$('#optin_title').on('input', function (e) {
    var trimmedTitle = stringTrimmer($('#optin_title').val(), 120)
    $('#pe-optin-6-title').text(trimmedTitle);
});

var httpsPopupSet = 0;
// initially this is loaded on page load for the purpose of prefill subscription dialogbox settings.
$(document).ready(function () {
    // default show the HTTPS tab.
    var setSiteType = 'https';
    if (setSiteType == "https") {
        $('#http-single-step-popup,.pushengagesweet-alert-optin-4').slideUp();
        $('#site_type').val("1");
        $('#set_site_type').val("https");
        httpsPopupSet = 1;
        var rawTitle = "<?php echo esc_html($optin_title_https) ?: '';?>";
        var trimmedTitle = escapeHtmlChars(stringTrimmer(rawTitle, 120));
        $('#optin_title').val(trimmedTitle);
        $('#optin_allow_btn_txt').val(escapeHtmlChars("<?php echo esc_html($optin_allow_button_https) ?: '';?>"));
        $('#optin_close_btn_txt').val(escapeHtmlChars("<?php echo esc_html($optin_close_button_https) ?: '';?>"));
        $('[name=optin_delay]').val("<?php echo esc_html($optin_delay_time_https); ?>");
        $dialogbox_type = "<?php echo esc_html($dialogbox_type_https); ?>";
        // if optin_type = 5 assume it as a optin_type = 4
        if($dialogbox_type == "5"){
            $("[name=optin_type]").val("4");
        } else {
            $("[name=optin_type]").val("<?php echo esc_html($dialogbox_type_https) ?: '';?>").change();
        }
        $('.quick-install-box').show();
        // hide the quick install option and slide up the "optin title", "allow btn"  and "block btn"
        if ($("[name=optin_type]").val() == 4) {
            $('.quick-install-box').hide();
            $('#hide-thanku-https').slideUp();
        }
        update_optin_ui();
        $('#intermediate-page-hide').hide();
    }
    else {
        $('#http-single-step-popup,.pushengagesweet-alert-optin-4').slideDown();
        $('#hide-thanku-https').slideDown();
        $('#site_type').val("0");
        $('#set_site_type').val("http");
        httpsPopupSet = 0;
        var rawTitle = "<?php echo esc_html($optin_title) ?: '';?>";
        var trimmedTitle = escapeHtmlChars(stringTrimmer(rawTitle, 120));
        $('#optin_title').val(trimmedTitle);
        $('#optin_allow_btn_txt').val(escapeHtmlChars("<?php echo esc_html($optin_allow_button) ?: '';?>"));
        $('#optin_close_btn_txt').val(escapeHtmlChars("<?php echo esc_html($optin_close_button) ?: '';?>"));
        $('#optin_delay').val("<?php echo esc_html($optin_delay_time); ?>");
        $("[name=optin_type]").val("<?php echo esc_html($dialogbox_type) ?: '';?>").change();
        $('.quick-install-box').hide();
        update_optin_ui();
        $('#intermediate-page-hide').slideDown();
        $('#intermediate-page-hide').show();
    }
});


// show and hide intemediate page based on status of quick install status.
function showAndHideIntermediatePopupForQuickInstall() {
    var siteTypeValue = $('#set_site_type').val();
    var optinValue = $("[name=optin_type]").val();
    if (siteTypeValue == "https") {
        var quickInstallSwitchValue = $('#quick-install-switch').is(":checked");

        if (quickInstallSwitchValue) {
            $('#popup-3').text("2");
            $('#second-sub-popup').hide();

        } else if(optinValue == 4){
            $('#second-sub-popup').hide();

        } else {
            $('#second-sub-popup').show();
        }

        if (optinValue == 4 || optinValue == 5) {
            optinValue = false;
        }
        else {
            optinValue = true;
        }
        if (quickInstallSwitchValue && optinValue) {
            $('#intermediate-page-hide').show();
        }
        else {
            $('#intermediate-page-hide').hide();
        }
    }
    else if (siteTypeValue == "http" && optinValue == 4) {
        $('#intermediate-page-hide').hide();
    }
    else {
        $('#intermediate-page-hide').show();
    }
}

// handle the HTTP/HTTPS tab for subscription dialogbox setting.
$('label.label-default').click(function () {
    hideSingleStepPopupHH();
});

// remove &quot; to {{"}} in the javscript for the values comming from db.
function removeQuotes(stringWithQuotes) {
    var newchar = '"';
    return stringWithQuotes.split(' &quot;').join(newchar);
}

// this function updates the preview and text field on the tab change.
// ":checked" means HTTP site.
function hideSingleStepPopupHH() {
    if ($('#switch-site-type').is(":checked")) {
        $('#http-single-step-popup,.pushengagesweet-alert-optin-4').slideDown();
        $('#hide-thanku-https').slideDown();
        $('#site_type').val("0");
        $('#set_site_type').val("http");
        httpsPopupSet = 0;
        var rawTitle = "<?php echo esc_html($optin_title) ?: '';?>";
        var trimmedTitle = escapeHtmlChars(stringTrimmer(rawTitle, 120));
        $('#optin_title').val(trimmedTitle);
        $('#optin_allow_btn_txt').val(escapeHtmlChars("<?php echo esc_html($optin_allow_button) ?: '';?>"));
        $('#optin_close_btn_txt').val(escapeHtmlChars("<?php echo esc_html($optin_close_button) ?: '';?>"));
        $('#optin-message').val("<?php !empty($optin_message) ?$optin_message: '';?>");
        $('#optin-allow-button-footer').val("<?php echo !empty($optin_allow_button_footer) ? esc_html($optin_allow_button_footer): '';?>");
        $('#optin-close-button-footer').val("<?php echo !empty($optin_close_button_footer) ? esc_html($optin_close_button_footer): '';?>");
        $('#optin-img-url').val("<?php echo !empty($optin_img_url) ? esc_url($optin_img_url): '';?>");
        $('#optin-footer-txt').val("<?php echo !empty($optin_footer_txt) ? esc_html($optin_footer_txt): '';?>");
        $('#optin-bg').val("<?php echo !empty($optin_bg) ? esc_html($optin_bg): '';?>");
        $('#optin_delay').val("<?php echo esc_html($optin_delay_time); ?>");
        $("[name=optin_type]").val("<?php  echo esc_html($dialogbox_type); ?>").change();
        update_optin_ui_text()
        update_optin_ui();
        $('#intermediate-page-hide').show();
        $('.quick-install-box').hide();
        $('#second-sub-popup').hide();
        // hide single-step-optin optins
        $('#single-step-optin-dialogbox').hide();

        changeOptinColor("<?php echo !empty($optin_bg)?esc_html($optin_bg):'';?>");
        showAndHideIntermediatePopupForQuickInstall();
    }
    else {
        $('#http-single-step-popup,.pushengagesweet-alert-optin-4').slideUp();
        $('#site_type').val("1");
        $('#set_site_type').val("https");
        httpsPopupSet = 1;
        var rawTitle = "<?php echo !empty($optin_title_https) ?esc_html($optin_title_https): '';?>";
        var trimmedTitle = escapeHtmlChars(stringTrimmer(rawTitle, 120));
        $('#optin_title').val(trimmedTitle);
        $('#optin_allow_btn_txt').val(escapeHtmlChars("<?php echo !empty($optin_allow_button_https) ?esc_html($optin_allow_button_https): '';?>"));
        $('#optin_close_btn_txt').val(escapeHtmlChars("<?php echo !empty($optin_close_button_https) ?esc_html($optin_close_button_https): '';?>"));
        $('#optin_delay').val("<?php echo esc_html($optin_delay_time_https); ?>");
        $('#optin-message').val("<?php echo !empty($optin_message_https) ?esc_html($optin_message_https): '';?>");
        $('#optin-allow-button-footer').val("<?php echo !empty($optin_allow_button_footer_https) ?esc_html($optin_allow_button_footer_https): '';?>");
        $('#optin-close-button-footer').val("<?php echo !empty($optin_close_button_footer_https) ?esc_html($optin_close_button_footer_https): '';?>");
        $('#optin-img-url').val("<?php echo !empty($optin_img_url_https) ?esc_html($optin_img_url_https): '';?>");
        $('#optin-footer-txt').val("<?php echo !empty($optin_footer_txt_https) ?esc_html($optin_footer_txt_https): '';?>");
        $('#optin-bg').val("<?php echo !empty($optin_bg_https) ?esc_html($optin_bg_https): '';?>");
        $("[name=optin_type]").val("<?php echo $dialogbox_type_https;?>").change();
        $('.quick-install-box').show();
        // hide single-step-optin optins
        $('#single-step-optin-dialogbox').show();

        if ($("[name=optin_type]").val() == 4) {
            $('#hide-thanku-https').slideUp();
            $('.quick-install-box').hide();
        }
        update_optin_ui_text()
        update_optin_ui();
        $('#intermediate-page-hide').hide();
        showAndHideIntermediatePopupForQuickInstall();
        changeOptinColor("<?php echo !empty($optin_bg_https)?esc_html($optin_bg_https):'';?>");
        if ($("[name=optin_type]").val() == 8)
            $('#quick-install-switch').prop('checked', true);
    }
}


// this function is used to hide subscription intermediate form and subscription intermediate page preview.
function hideIntermediatePage() {
    if ($('#switch-site-type').is(":checked")) {
        $('#intermediate-page-hide').hide();
    }
    else {
        $('#intermediate-page-hide').show();
    }
    showAndHideIntermediatePopupForQuickInstall();
}


// start optin type bell swing functionality.
var PEswingwell = "";
function PESwingWellSetOption4() {
    PEswingwell = setInterval(function () {
        startWellSwing()
    }, 1000);
}

function startWellSwing() {
    var elements = document.getElementsByClassName('fa fa-bell PEoption4bell');
    for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        if (element.className == 'fa fa-bell PEoption4bell')
            element.className += ' PEnotioption4-swing';
        else
            element.className = 'fa fa-bell PEoption4bell';
    }
}
PESwingWellSetOption4();
// end optin type bell swing functionality.

var _peapp = {
    "app_image": "<?php echo esc_html($appdata['site_image']);?>",
    "app_poweredby": "<?php  if ($appdata['is_whitelabel'] = 1) echo 'powered by Pushengage';?>",
    "app_url": "http://www.pushengage.com"
};
<?php $settings = (isset($appdata['optin_settings']) && $appdata['optin_settings']) ? $appdata['optin_settings'] : "''"; ?>
var _pe_optin_settings =<?php echo $settings;?>;

var htmlbody = document.getElementsByTagName("BODY")[0];

// this function is updating the text in the optin dialogbox preview.
// at the first time rendering of the page.
function update_optin_ui_text() {
    var trimmedTitle = escapeHtmlChars(stringTrimmer($('#optin_title').val(), 120));
    $("#_pe_optin_settings_optin_title").text(trimmedTitle);
    _pe_optin_settings.desktop.optin_title = trimmedTitle;
    $("#pushengage_allow_btn").text(escapeHtmlChars($("#optin_allow_btn_txt").val()));
    $("#pushengage_allow_btn").val(stringTrimmer(escapeHtmlChars($("#optin_allow_btn_txt").val()), 6));
    _pe_optin_settings.desktop.optin_allow_btn_txt = escapeHtmlChars($("#optin_allow_btn_txt").val());
    $("#pushengage_close_btn").text(stringTrimmer(escapeHtmlChars($("#optin_close_btn_txt").val()), 6));
    _pe_optin_settings.desktop.optin_close_btn_txt = escapeHtmlChars($("#optin_close_btn_txt").val());
    $("#page_heading_view").text(escapeHtmlChars($("#page_heading").val()));
    $("#page_tagline_view").text(escapeHtmlChars($("#page_tagline").val()));
}

// this function fills the diloag box input fields parallely on filling the form.
// this also updates the preview & input form on changing the optin type.
$(document).ready(function () {
    $("#optin_title").keyup(function () {
        var trimmedTitle = escapeHtmlChars(stringTrimmer($("#optin_title").val(), 120));
        if(_pe_optin_settings.desktop.http.optin_type==2 || _pe_optin_settings.desktop.https.optin_type==2 || _pe_optin_settings.desktop.http.optin_type==1 || _pe_optin_settings.desktop.https.optin_type==1) {
            $("#_pe_optin_settings_optin_title").text(trimmedTitle);
        }

        $(".pe_title").text(trimmedTitle);
        _pe_optin_settings.desktop.optin_title = trimmedTitle;
    });
    $("#optin_allow_btn_txt").keyup(function () {
        $("#pushengage_allow_btn").text(stringTrimmer(escapeHtmlChars($("#optin_allow_btn_txt").val()), 6));
        $("#pushengage_allow_btn").val(stringTrimmer(escapeHtmlChars($("#optin_allow_btn_txt").val()), 6));
        _pe_optin_settings.desktop.optin_allow_btn_txt = escapeHtmlChars($("#optin_allow_btn_txt").val());
    });
    $("#optin_close_btn_txt").keyup(function () {
        $("#pushengage_close_btn").val(stringTrimmer(escapeHtmlChars($("#optin_close_btn_txt").val()), 6));
        $("#pushengage_close_btn").text(stringTrimmer(escapeHtmlChars($("#optin_close_btn_txt").val()), 6));
        _pe_optin_settings.desktop.optin_close_btn_txt = escapeHtmlChars($("#optin_close_btn_txt").val());
    });
    $("#page_heading").keyup(function () {
        $("#page_heading_view").text(escapeHtmlChars($("#page_heading").val()));
    });
    $("#page_tagline").keyup(function () {
        $("#page_tagline_view").text(escapeHtmlChars($("#page_tagline").val()));
    });

    // update UI, when optin_type changes.
    $("[name=optin_type]").change(function () {
        update_optin_ui_text();
        update_optin_ui();
    });

    update_optin_ui_text();
    update_optin_ui();
});

// trim the string and append tripple dot(i.e, ...) according to the required size.
function stringTrimmer(text = '', finalLength, startIndex = 0) {
    var trimmedString = text.substr(startIndex, finalLength);
    if(text.length > finalLength) {
        return trimmedString+'...';
    }
    return trimmedString;
}

function escapeHtmlChars(value) {
    if(!value) {
        return;
    }

    var e = document.createElement('span');
    e.innerHTML = value;
    return e.textContent
}

function changeOptinColor(optinColor) {
    if (optinColor) {
        $('.pe-optin-7').css({"background": "" + optinColor});
        $('#ls_pushengage_allow_btn,#ls_pushengage_close_btn').css({"border-color": "" + optinColor});
    }
    else {
        $('.pe-optin-7,#ls_pushengage_allow_btn,#ls_pushengage_close_btn').removeAttr("style")
    }
}

// this function is used for update the optin preview in the UI.
// based on the respective selected optin type in the optin select options.
function update_optin_ui() {
    if (httpsPopupSet == 1) {
        $('.quick-install-box').show();
    }

    if ($("[name=optin_type]").val()) {
        $(".dialogbox-property").show();
        $("#optin_title_label").text("Optin Title");
    }

    switch ($("[name=optin_type]").val()) {
        case "1":
            $("#pushengage_confirm").remove();
            $("#right_workspace").html("<div id='pushengage_confirm' style='display: inline-block;width:410px;top:0px;left:33%;border: 1px solid #D0D0D0;background: #EFEFEF;padding:15px;-webkit-border-radius: 3px;-moz-border-radius: 3px;border-radius: 3px;box-shadow: 1px 1px 3px #DCDCDC;z-index: 999999;'><div style='float: left;padding: -1px;margin-right: 8px;width:80px;height:80px;' id='pushengage_client_img'><img src='" + _peapp.app_image + "' style='width: 87px;'></div>  <div style='font-family: arial;font-size: 15px;font-weight: 600;color: #4A4A4A;' id='_pe_optin_settings_optin_title'>" + escapeHtmlChars(stringTrimmer(_pe_optin_settings.desktop.optin_title, 120)) + "</div>  <div style='clear: both;'><div style='float: left;font-family: arial;font-size: 9px;padding-top: 10px;'>" + _peapp.app_poweredby + "</div><div style='float: right;font-family: arial;padding: 1px 19px;font-size: 15px;background-color: #2ecc71;color: #fff;border: 1px solid #7FB797;border-radius: 4px;cursor:pointer;width:104px;overflow:hidden;height:32px;' id='pushengage_allow_btn' >" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_allow_btn_txt), 6) + "</div><div style='float: right;font-family: arial;font-size: 15px;padding: 1px 19px;background-color: #fff;border-radius: 5px;border: 1px solid #D6D1D1;margin-right: 7px;cursor:pointer;width:104px;overflow:hidden;height:32px;' id='pushengage_close_btn'>" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_close_btn_txt), 6) + "</div>  </div>  </div> ");
            hideIntermediatePage();
            $('#hide-thanku-https').show();
            hideLargeSafariBoxExtraInput();
            break;

        case "2":
            $("#pushengage_confirm").remove();
            htmlbody.insertAdjacentHTML('beforeend', "<div id='pushengage_confirm' class='optin-3 optin-floatin' style='transition-duration: 1.5s;'><div class='pe_logo'><img src='" + _peapp.app_image + "'></div><div class='pe_title' id='_pe_optin_settings_optin_title' style='word-break: break-all;' >" + escapeHtmlChars(stringTrimmer(_pe_optin_settings.desktop.optin_title, 120)) + "</div><div class='pe_buttons'><input type='button' value='" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_allow_btn_txt), 6) + "' id='pushengage_allow_btn' class='pe_btn-allow allow-btn'><input type='button' style='float: right;font-family: Open Sans, sans-serif;padding: 0px 19px;font-size: 19px;background-color: #2ecc71;color: #fff;border: 1px solid #7FB797;border-radius: 4px;cursor:pointer;vertical-align: bottom !important;height: 35px;box-shadow: none;margin-left: 10px;width:141px'value='" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_close_btn_txt), 6) + "' id='pushengage_close_btn' class='pe_btn-close close-btn'></div><div class='pe_branding'><a href='http://www.pushengage.com/' target='_blank'>" + _peapp.app_poweredby + "</a></div></div>");
            hideIntermediatePage();
            $('#hide-thanku-https').show();
            hideLargeSafariBoxExtraInput();
            break;

        case "3":
            $("#pushengage_confirm").remove();
            htmlbody.insertAdjacentHTML('beforeend', "<div id='pushengage_confirm' class='PE-optin4'><div class='PE-optin4-box PE-arrow_box '><div class='PE-optin4-image' style='padding-top:10px' ><img src='" + _peapp.app_image + "' style='border-radius:50%'></div><div class='PE-optin4-text'><span id='PEnoti-close-pane' onclick='PESwingWellSetOption4();'><i class='fa fa-close'></i></span><i id='pushengage_close_btn'></i><div class='PE-title PE-optin4-heading' style='padding-top:10px' id='_pe_optin_settings_optin_title'>" + escapeHtmlChars(stringTrimmer(_pe_optin_settings.desktop.optin_title, 120)) + "</div></div><div class='PE-optin4-btns'><input type='button' class='PE-push-btn PE-btn-allow'  value='" + escapeHtmlChars(_pe_optin_settings.desktop.optin_allow_btn_txt) + "'></div><div class='PE-branding'><a href='https://www.pushengage.com' target='_blank'>" + _peapp.app_poweredby + "</a></div></div><div class='PE-optin4-bell' ><i class='fa fa-bell PEoption4bell PEnotioption4-swing'></i></div></div>");
            hideIntermediatePage();
            $('#hide-thanku-https').hide();
            $(".dialogbox-property").hide();
            hideLargeSafariBoxExtraInput();
            break;

        case ("4" || "5"):
            $("#pushengage_confirm").remove();
            $(".dialogbox-property").hide();
            $("#optin_title_label").text("Thank You Text");
            $("#right_workspace").html("<div id='pushengage_confirm'><div id='http-single-step-popup' class='alert-browser-notification-popup arrow_box'><span class='notification-content-close'>&#10006;</span><p class='alert-browser-notification-popup-url'><?php echo esc_url($site_subdomain_for_browser_popup) . "."?>pushengage.com want to:</p><p class='alert-browser-notification-popup-show'><img src='<?php echo PUSHENGAGE_PLUGIN_URL . 'images/bell.png'?>'; style='width:18px;margin: 0 8px 0 23px;height: 15px;'></img>Show notifications</p><p style='text-align:right'><a class='notification-allow'>Allow</a><a class='notification-close'>Block</a></p></div></div>");
            $('.quick-install-box').hide();
            $('#second-sub-popup').hide();
            hideIntermediatePage();
            hideLargeSafariBoxExtraInput();
            break;

        case "6":
            $("#pushengage_confirm").remove();
            $("#right_workspace").html("<div id=pushengage_confirm style=background:#fff class=pe-optin-6><div id=pe-optin-6-body><div id=pe-optin-6-title style=font-family:arial;font-size:17px;font-weight:500;color:#232323;line-height:24px>" + stringTrimmer(_pe_optin_settings.desktop.optin_title, 120) + "</div><img src='" + _peapp.app_image + "' alt='site image' id='pe-optin-6-site-img'><div id=pe-optin-6-action><div id=pe-optin-6-cancel-btn-wrapper><div id=pushengage_close_btn style=font-family:arial;font-size:18px;font-weight:500;color:#232323;background:#fff;width:104px;overflow:hidden;height:32px; class=pe-optin-6-cancel-btn>" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_close_btn_txt), 6) + "</div></div><div id=pe-optin-6-allow-btn-wrapper><div id=pushengage_allow_btn style=font-family:arial;font-size:18px;font-weight:500;color:#fff;background:#2ecc71;width:104px;overflow:hidden;height:32px; class=pe-optin-6-allow-btn>" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_allow_btn_txt), 6) + "</div></div></div><div id=pe-optin-6-powered>powered by Pushengage</div></div></div>");
            hideIntermediatePage();
            $('#hide-thanku-https').show();
            hideLargeSafariBoxExtraInput();
            break;

        case "8":
            $("#pushengage_confirm").remove();
            $("#right_workspace").html("<div id=pushengage_confirm style=background:#fff class=pe-optin-6><div id=pe-optin-6-body><img src='" + _peapp.app_image + "' alt='site image' id='pe-optin-6-site-img' style='margin-bottom: 15px;'><div id=pe-optin-6-title style=font-family:arial;font-size:17px;font-weight:600;color:#232323;line-height:24px>" + stringTrimmer(_pe_optin_settings.desktop.optin_title, 120) + "</div><div class='radio radio-info radio-inline' style='margin-top: 25px; margin-left: 50px;'><input type='radio' id='pe_segment_1' value='' name='radioInline' checked><label for='inlineRadio1' style='margin-right: 50px;' class='lbl_segment1'> Segment1 </label><input type='radio' id='pe_segment_2' value='' name='radioInline'><label class='lbl_segment2' for='inlineRadio1'> Segment2 </label></div><div id=pe-optin-6-action><div id=pe-optin-6-cancel-btn-wrapper><div id=pushengage_close_btn style=font-family:arial;font-size:18px;font-weight:500;color:#232323;background:#fff;width:104px;overflow:hidden;height:32px; class=pe-optin-6-cancel-btn>" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_close_btn_txt), 6) + "</div></div><div id=pe-optin-6-allow-btn-wrapper><div id=pushengage_allow_btn style=font-family:arial;font-size:18px;font-weight:500;color:#fff;background:#2ecc71;width:104px;overflow:hidden;height:32px; class=pe-optin-6-allow-btn>" + stringTrimmer(escapeHtmlChars(_pe_optin_settings.desktop.optin_allow_btn_txt), 6) + "</div></div></div><div id=pe-optin-6-powered>powered by Pushengage</div></div></div>");
            hideIntermediatePage();
            $('#hide-thanku-https').show();
            $("#optin_message").removeProp('required');
            $('#quick-install-switch').prop('checked');
            $('#popup-3').text("2");
            hideLargeSafariBoxExtraInput();
            var seg_names = $("#segments").val();
            if (seg_names != undefined && seg_names != null && seg_names.length > 0) {
                if (seg_names.length == 1) {
                    $('.lbl_segment1').html(seg_names[0]);
                    $('.lbl_segment2').html('Segment2');
                }
                else {
                    $('.lbl_segment1').html(seg_names[0]);
                    $('.lbl_segment2').html(seg_names[1]);
                }

            } else {
                $('.lbl_segment1').html('Segment1');
                $('.lbl_segment2').html('Segment2');
            }
            break;
    }
};

// in case of general setting, subscription dialog box and automatic segmentation hide the "#pushengage_confirm" container.
// since its appedning dynamically.
$("#li_pe_general_setting,#li_pe_welcome_notification,#li_pe_automatic_segmentation").click(function () {
    $("#pushengage_confirm").hide();
});

$("#li_pe_subscription_dialogbox").click(function () {
    $("#pushengage_confirm").show();
});

if (!(<?php echo '"' . $tab . '"'; ?>== "subDialogbox")) {
    $("#gSettings").ready(function () {
        $("#pushengage_confirm").hide();
    });
}

$('#create_automatic_segmentation').click(function() {
    if($('#pe-create-segment_name').val()) {
        $('#create_automatic_segmentation').html('<i class="fa fa-spinner fa-spin"></i>Creating')
    }
});

$('#update_automatic_segmentation').click(function() {
    if($('#pe-edit-segment_name').val()) {
        $('#update_automatic_segmentation').html('<i class="fa fa-spinner fa-spin"></i>Updating')
    }
});

// handling the addition of input field.
var peIncludeSegmentRuleCount = 1;
var peExcludeSegmentRuleCount = 1;
var peEditIncludeSegmentRuleCount;
var peEditExcludeSegmentRuleCount;

// On clicking plus button('+') of include criteria add a new input field for segment pattern.
// In case of edit auto-segment modal, we need two id's
// "pe_edit_include_pattern_value_<<series_no>>" and "pe_edit_include_pattern_value_<<series_no>>"
function addPeIncludeSegmentContainer(e, isEditing = false) {
    var peIncludeSegmentSeriesId = e.id;
    var currentSeriesNumber = peIncludeSegmentSeriesId.split('_')[4];

    if(isEditing) {
        if($('#pe_edit_include_pattern_value_'+currentSeriesNumber).val() != '') {

            // to remove "+" and unhide "delete" button
            $('#pe_add_include_pattern_'+currentSeriesNumber).remove();
            $('#pe_include_pattern_container_'+currentSeriesNumber+ ' .pe_remove_include_pattern').removeClass('hide');

            var peIncludePatternHTML = '<div id="pe_include_pattern_container_'+peEditIncludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_include_pattern_rule_options_'+peEditIncludeSegmentRuleCount+'" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" id="pe_edit_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" style="width:60%" /> <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="addPeIncludeSegmentContainer(this, true)"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
            $("#pe_edit_include_pattern").append(peIncludePatternHTML);
            $('#pe_edit_include_segment_count').val(peEditIncludeSegmentRuleCount);
            peEditIncludeSegmentRuleCount++;

        }
    } else {
        if($('#pe_include_pattern_value_'+currentSeriesNumber).val() != '') {

            // to remove "+" and unhide "delete" button
            $('#pe_add_include_pattern_'+currentSeriesNumber).remove();
            $('#pe_include_pattern_container_'+currentSeriesNumber+ ' .pe_remove_include_pattern').removeClass('hide');

            peIncludeSegmentRuleCount++;
            var peIncludePatternHTML = '<div id="pe_include_pattern_container_'+peIncludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_include_pattern_rule_options_'+peIncludeSegmentRuleCount+'" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_include_pattern_value_'+peIncludeSegmentRuleCount+'" id="pe_include_pattern_value_'+peIncludeSegmentRuleCount+'" style="width:60%" /> <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_include_pattern_'+peIncludeSegmentRuleCount+'" onClick="addPeIncludeSegmentContainer(this)"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_'+peIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
            $("#pe_include_pattern").append(peIncludePatternHTML);
            $('#pe_include_segment_count').val(peIncludeSegmentRuleCount);
        }
    }
}

// On clicking plus button('+') of exlude criteria, to add a new input field for segment pattern.
// In case of edit auto-segment modal, we need two id's
// "pe_edit_exclude_pattern_value_<<series_no>>" and "pe_edit_exclude_pattern_value_<<series_no>>"
function addPeExcludeSegmentContainer(e, isEditing = false) {
    var peExcludeSegmentSeriesId = e.id;
    var currentSeriesNumber = peExcludeSegmentSeriesId.split('_')[4];
    if(isEditing) {
        if($('#pe_edit_exclude_pattern_value_'+currentSeriesNumber).val() != '') {

            // to remove "+" and unhide "delete" button
            $('#pe_add_exclude_pattern_'+currentSeriesNumber).remove();
            $('#pe_exclude_pattern_container_'+currentSeriesNumber+ ' .pe_remove_exclude_pattern').removeClass('hide');

            var peIncludePatternHTML = '<div id="pe_exclude_pattern_container_'+peEditExcludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_exclude_pattern_rule_options_'+peEditExcludeSegmentRuleCount+'" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" id="pe_edit_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" style="width:60%" /> <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="addPeExcludeSegmentContainer(this, true)"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
            $("#pe_edit_exclude_pattern").append(peIncludePatternHTML);
            $('#pe_edit_exclude_segment_count').val(peEditExcludeSegmentRuleCount);
            peEditExcludeSegmentRuleCount++;
        }
    } else {
        if($('#pe_exclude_pattern_value_'+currentSeriesNumber).val() != '') {

            // to remove "+" and unhide "delete" button
            $('#pe_add_exclude_pattern_'+currentSeriesNumber).remove();
            $('#pe_exclude_pattern_container_'+currentSeriesNumber+ ' .pe_remove_exclude_pattern').removeClass('hide');

            peExcludeSegmentRuleCount++;
            var peIncludePatternHTML = '<div id="pe_exclude_pattern_container_'+peExcludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_exclude_pattern_rule_options_'+peExcludeSegmentRuleCount+'" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact">Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_'+peExcludeSegmentRuleCount+'" id="pe_exclude_pattern_value_'+peExcludeSegmentRuleCount+'" style="width:60%" /> <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_exclude_pattern_'+peExcludeSegmentRuleCount+'" onClick="addPeExcludeSegmentContainer(this)"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_'+peExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
            $("#pe_exclude_pattern").append(peIncludePatternHTML);
            $('#pe_exclude_segment_count').val(peExcludeSegmentRuleCount);
        }
    }

}
// On clicking minus button('-') of inlude criteria remove the selected input field for segment pattern.
function removePeIncludeSegmentContainer(e) {
    var peExcludeSegmentSeriesId = e.id;
    var currentSeriesNumber = peExcludeSegmentSeriesId.split('_')[4];
    $("#pe_include_pattern_container_"+currentSeriesNumber).remove();
}

// On clicking minus button('-') of exlude criteria remove the selected input field for segment pattern.
function removePeExcludeSegmentContainer(e) {
    var peExcludeSegmentSeriesId = e.id;
    var currentSeriesNumber = peExcludeSegmentSeriesId.split('_')[4];
    $("#pe_exclude_pattern_container_"+currentSeriesNumber).remove();
}


// ajax call to delete auto-segment.
function deletePeAutoSegment(e, isModal=false) {

    if(!isModal) {
        var segment_id = e.getAttribute('data-segment-id');
        $('#pe-confirm-delete-auto-segment').attr('data-segment-id',segment_id);
        return;
    }

    $('#pe-confirm-delete-auto-segment').html('<i class="fa fa-spinner fa-spin"></i>Deleting')
    var segment_id = e.getAttribute('data-segment-id');
    var pe_api_key = "<?php echo $pushengage_settings['appKey'] ?>";
    var requestData = new Object();
    requestData["segment_id"] = segment_id;

    $.ajax({
      method: "DELETE",
      url: "https://api.pushengage.com/apiv1/AutomaticSegmentation/deleteAutomaticSegments/"+segment_id,
      headers: {
        'api_key': pe_api_key
      },
      data: requestData,
    }).done(function(data) {
        $('#pe-confirm-delete-auto-segment').html('Delete')
        $('#pe-delete-automatic-segment-modal').modal('hide');
        // refresh the page to get the latest auto-segment list.
        location.reload();
    }).fail(function(){
        $('#pe-confirm-delete-auto-segment').html('Delete')
        $('#pe-delete-automatic-segment-modal').modal('hide');
    })
}

// ajax call to get the auto-segment details the particlular selected auto-segment to Edit Auto-Segment.
// And appeding the segment details with the HTMl containers.
function editPeAutoSegment(e) {

    var segment_id = e.getAttribute('data-segment-id');
    var pe_api_key = "<?php echo $pushengage_settings['appKey'] ?>";

    $.ajax({
      method: "GET",
      url: "https://api.pushengage.com/apiv1/AutomaticSegmentation/getAutomaticSegmentBySegmentId/"+segment_id,
      headers: {
        'api_key': pe_api_key
      },
    }).done(function( data ) {
       if(data.count > 0) {
            // these two variable are declared above to count the segment rule and give them incremented series id on new addition.
            peEditIncludeSegmentRuleCount = 1;
            peEditExcludeSegmentRuleCount = 1;
            // clean up previously appended HTML containers of previous segment before editing another auto-segment
            $('#pe_edit_include_pattern').children().remove();
            $('#pe_edit_exclude_pattern').children().remove();

            if(data.segments[0] && data.segments[0].segment_id) {
                $('#pe_segment_id').val(data.segments[0].segment_id);
            }
            if(data.segments[0] && data.segments[0].segment_name) {
                $('#pe-edit-segment_name').val(data.segments[0].segment_name);
            }
            if(data.segments[0] && data.segments[0].add_segment_on_page_load == "1") {
               $('#pe_edit_segment_page_visit').prop('checked', true);
            }
            if(data.segments[0] && data.segments[0].segment_criteria) {

                var segmentCriteria = data.segments[0].segment_criteria;
                var includeSegmentHTML = '';
                var excludeSegmentHTML = '';
                var include_start_rules = [];
                var include_contains_rules = [];
                var include_exact_rules = [];
                var exclude_start_rules = [];
                var exclude_contains_rules = [];
                var exclude_exact_rules = [];

                if(segmentCriteria.include && segmentCriteria.include.length > 0) {
                    segmentCriteria.include.forEach(function(rules) {
                        switch(rules.rule){
                            case 'start':
                                include_start_rules.push(rules.value);
                                break;
                            case 'contains':
                                include_contains_rules.push(rules.value);
                                break;
                            case 'exact':
                                include_exact_rules.push(rules.value);
                                break;
                            default:
                                // do nothing
                        }
                    })
                }

                if(segmentCriteria.exclude && segmentCriteria.exclude.length > 0) {
                    segmentCriteria.exclude.forEach(function(rules) {
                        switch(rules['rule']){
                            case 'start':
                                exclude_start_rules.push(rules.value);
                                break;
                            case 'contains':
                                exclude_contains_rules.push(rules.value);
                                break;
                            case 'exact':
                                exclude_exact_rules.push(rules.value);
                                break;
                            default:
                                // do nothing
                        }
                    })
                }

                // appedning HTML containers in case of exclude segments in case of edit.
                if(include_start_rules && include_start_rules.length > 0) {
                    include_start_rules.forEach(function(url_pattern) {
                        includeSegmentHTML += '<div id="pe_include_pattern_container_'+peEditIncludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_include_pattern_rule_options_'+peEditIncludeSegmentRuleCount+'" value="start" ><option value="start" selected>Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" id="pe_edit_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" value="'+url_pattern+'" style="width:60%" /> <button type="button" class="btn btn-danger pe_remove_include_pattern" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                        peEditIncludeSegmentRuleCount++;
                    });
                }
                if(include_contains_rules && include_contains_rules.length > 0) {
                    include_contains_rules.forEach(function(url_pattern) {
                        includeSegmentHTML += '<div id="pe_include_pattern_container_'+peEditIncludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_include_pattern_rule_options_'+peEditIncludeSegmentRuleCount+'" value="contains" ><option value="start" >Start With</option><option value="contains" selected>Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" id="pe_edit_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" value="'+url_pattern+'" style="width:60%" /> <button type="button" class="btn btn-danger pe_remove_include_pattern" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                        peEditIncludeSegmentRuleCount++;
                    });
                }
                if(include_exact_rules && include_exact_rules.length > 0) {
                    include_exact_rules.forEach(function(url_pattern) {
                        includeSegmentHTML += '<div id="pe_include_pattern_container_'+peEditIncludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_include_pattern_rule_options_'+peEditIncludeSegmentRuleCount+'" value="exact" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" selected>Exact Match</option></select> <input type="text" name="pe_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" id="pe_edit_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" value="'+url_pattern+'" style="width:60%" /> <button type="button" class="btn btn-danger pe_remove_include_pattern" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                        peEditIncludeSegmentRuleCount++;
                    })
                }

                if(segmentCriteria.include && segmentCriteria.include.length > 0) {
                    peEditIncludeSegmentRuleCount = segmentCriteria.include.length + 1;
                    includeSegmentHTML += '<div id="pe_include_pattern_container_'+peEditIncludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_include_pattern_rule_options_'+peEditIncludeSegmentRuleCount+'" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" id="pe_edit_include_pattern_value_'+peEditIncludeSegmentRuleCount+'" style="width:60%" /> <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="addPeIncludeSegmentContainer(this, true)"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_'+peEditIncludeSegmentRuleCount+'" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                    peEditIncludeSegmentRuleCount++;
                }

                if(peEditIncludeSegmentRuleCount == 1)  {
                    includeSegmentHTML += '<div id="pe_include_pattern_container_1"><select name="pe_include_pattern_rule_options_1" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_include_pattern_value_1" id="pe_edit_include_pattern_value_1" style="width:60%" /> <button type="button" class="btn btn-success pe_add_include_pattern" id="pe_add_include_pattern_1" onClick="addPeIncludeSegmentContainer(this, true)"><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_include_pattern hide" id="pe_remove_include_pattern_1" onClick="removePeIncludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                    peEditIncludeSegmentRuleCount++;
                }

                $("#pe_edit_include_pattern").append(includeSegmentHTML);
                $('#pe_edit_include_segment_count').val(peEditIncludeSegmentRuleCount);

                // appedning HTML containers in case of exclude segments in case of edit.
                if(exclude_start_rules && exclude_start_rules.length > 0) {
                    exclude_start_rules.forEach(function(url_pattern) {
                        excludeSegmentHTML += '<div id="pe_exclude_pattern_container_'+peEditExcludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_exclude_pattern_rule_options_'+peEditExcludeSegmentRuleCount+'" value="start" ><option value="start" selected>Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" id="pe_edit_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" value="'+url_pattern+'" style="width:60%" /> <button type="button" class="btn btn-danger pe_remove_exclude_pattern" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                        peEditExcludeSegmentRuleCount++;
                    });
                }
                if(exclude_contains_rules && exclude_contains_rules.length > 0) {
                    exclude_contains_rules.forEach(function(url_pattern) {
                        excludeSegmentHTML += '<div id="pe_exclude_pattern_container_'+peEditExcludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_exclude_pattern_rule_options_'+peEditExcludeSegmentRuleCount+'" value="contains" ><option value="start" >Start With</option><option value="contains" selected>Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" id="pe_edit_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" value="'+url_pattern+'" style="width:60%" /> <button type="button" class="btn btn-danger pe_remove_exclude_pattern" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                        peEditExcludeSegmentRuleCount++;
                    });
                }
                if(exclude_exact_rules && exclude_exact_rules.length > 0) {
                    exclude_exact_rules.forEach(function(url_pattern) {
                        excludeSegmentHTML += '<div id="pe_exclude_pattern_container_'+peEditExcludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_exclude_pattern_rule_options_'+peEditExcludeSegmentRuleCount+'" value="exact" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" selected>Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" id="pe_edit_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" value="'+url_pattern+'" style="width:60%" /> <button type="button" class="btn btn-danger pe_remove_exclude_pattern" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                        peEditExcludeSegmentRuleCount++;
                    })
                }

                if(segmentCriteria.exclude && segmentCriteria.exclude.length > 0) {
                    peEditExcludeSegmentRuleCount = segmentCriteria.exclude.length + 1;
                    excludeSegmentHTML += '<div id="pe_exclude_pattern_container_'+peEditExcludeSegmentRuleCount+'" style="margin-top: 8px;"><select name="pe_exclude_pattern_rule_options_'+peEditExcludeSegmentRuleCount+'" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" id="pe_edit_exclude_pattern_value_'+peEditExcludeSegmentRuleCount+'" style="width:60%" /> <button type="button" class="btn btn-success" id="pe_add_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="addPeExcludeSegmentContainer(this, true)" ><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_'+peEditExcludeSegmentRuleCount+'" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                }

                if(peEditExcludeSegmentRuleCount == 1) {
                    excludeSegmentHTML += '<div id="pe_exclude_pattern_container_1"><select name="pe_exclude_pattern_rule_options_1" value="start" ><option value="start" >Start With</option><option value="contains" >Contains</option><option value="exact" >Exact Match</option></select> <input type="text" name="pe_exclude_pattern_value_1" id="pe_edit_exclude_pattern_value_1" style="width:60%" /> <button type="button" class="btn btn-success" id="pe_add_exclude_pattern_1" onClick="addPeExcludeSegmentContainer(this, true)" ><i class="fa fa-plus" aria-hidden="true"></i></button><button type="button" class="btn btn-danger pe_remove_exclude_pattern hide" id="pe_remove_exclude_pattern_1" onClick="removePeExcludeSegmentContainer(this)"><i class="fa fa-trash" aria-hidden="true"></i></button></div>';
                }

                $("#pe_edit_exclude_pattern").append(excludeSegmentHTML);
                $('#pe_edit_exclude_segment_count').val(peEditExcludeSegmentRuleCount);

                // open the auto-segment editing modal to edit details.
                $('#pe-edit-automatic-segment-modal').modal();
            }
       }
    });
}

// ajax call to get all segments belongs to a given URL.
$('#pe-test-auto-segment').click(function( event ) {
    $('#pe-test-auto-segment').html('<i class="fa fa-spinner fa-spin"></i>Testing')

    var segment_url = $('#pe_segment_test_url').val();
    var pe_api_key = "<?php echo $pushengage_settings['appKey'] ?>";
    var options = {
      method: "POST",
      url: "https://api.pushengage.com/apiv1/AutomaticSegmentation/automaticSegmentsTestRules",
      headers: {
        'api_key': pe_api_key
      },
      data: JSON.stringify({
        "segment_url": segment_url
      }),
    };

    $.ajax(options).done(function(data) {
      $('#pe-test-auto-segment').html('Test')
        var testResultHTML = '';
        if(data.count > 0) {
            testResultHTML += '<label>Matching Segments : </label>';
            if(data && data.segments) {
                data.segments.forEach(function(segment, key) {
                    if(key == data.segments.length - 1) {
                        testResultHTML += '<span> '+segment+ '</span>';
                    } else {
                        testResultHTML += '<span> '+segment+ ',</span>';
                    }
                })
            }

        } else {
            testResultHTML += '<p><b>No segments matched </b><p>';
        }
        $('#pe-matched-auto-segment').children().remove();
        $('#pe-matched-auto-segment').append(testResultHTML);
    });

})


/**
 * 
 * Ajax call to create a pushengage custom segment.
 * 
 * @since 3.2.0
 */
$( "#create-pushengage-segment" ).click( function( event ) {

    $( "#create-pushengage-segment" ).html( "<i class='fa fa-spinner fa-spin'></i>Creating" )

    var segmentName = $( "#pe-custom-segment-name" ).val();
    var peApiKey = "<?php echo $pushengage_settings['appKey'] ?>";
    
    var options = {
      method: "POST",
      url: "https://api.pushengage.com/apiv1/segments",
      headers: {
        "api_key": peApiKey
      },
      data: {
        "segment_name": segmentName
      },
    };

    $.ajax( options ).done( function( data ) {
        var errorMessage = "";
        if ( data.success === false  && data.message ) {
            errorMessage += "<span style='color:red'> Error: "+data.message.toLowerCase()+ "</span>";
        } else {
            if ( data && data.success ) {
                $( ".select-pe-segment" ).append( "<option>"+segmentName+"</option>" );
                $( "#pe-create-segment-modal" ).modal("hide");
                $( "#pe-custom-segment-name" ).val( "" );
            } 
        }

        $( "#create-pushengage-segment" ).html( "Create" )
        $( "#pe-error-create-segment" ).children().remove();
        $( "#pe-error-create-segment" ).append( errorMessage );
    });
})

/**
 * 
 * Error handling when submitting update category segment settings form.
 * 
 * @since 3.2.0
 */
$( "#pe-update-category-segment-settings" ).click( function( event ){
    var totalCheckboxesCount = $( ".pe-category-segmentation-checkbox" ).length;

    // Check if any checkbox has checked and no any segment has selected then show an error msg.
    for ( var i = 0; i < totalCheckboxesCount; i++ ) {
        var segmentCheckboxCheckedStatus = $( "#segment_checkbox_"+i ).prop( "checked" );

        if ( segmentCheckboxCheckedStatus ) {
            var segmentName = $( "#segment_name_"+i ).val();

            if ( segmentName === "Select a segment" ) {
                var errorMessage = "<span style='color:red'> Please select a pushengage segment </span>";
                $( "#segment_name_"+i+"_error" ).children().remove();
                $( "#segment_name_"+i+"_error" ).append( errorMessage );
                event.preventDefault();
            } 
            
        } else {
                $( "#segment_name_"+i+"_error" ).children().remove();
        }
    }
});

</script>
