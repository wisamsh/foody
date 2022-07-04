<?php if (wp_is_mobile()) { ?>


    <div class="search-overlay floating-mobile-header d-lg-none">

        <div class="input-container">
            <span class="algolia-autocomplete" style="position: relative; display: inline-block; direction: ltr;">
                <span class="algolia-autocomplete" style="position: relative; display: inline-block; direction: ltr;">
                    <span class="bmd-form-group">
                        <input type="search" id="moile_search" class="search-autocomplete foody-input" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="both" aria-expanded="false" aria-owns="algolia-autocomplete-listbox-10" dir="auto" style="position: relative; vertical-align: top;"></span>

                    <span class="close">×</span>
        </div>
        <div class="overlay-white"> </div>
    </div>

    </div>




    <div class="sticky_bottom_header no-print">
        <div class="socials d-none d-lg-block">

            <section class="header-top-container  d-none d-lg-flex">

                <section class="social-icons">

                    <a href="https://www.facebook.com/FoodyIL/" target="_blank">
                        <i class="icon-facebook">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </a>

                    <a href="https://www.instagram.com/foody_israel" target="_blank">
                        <i class="icon-instagram">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                        </i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCy_lqFqTpf7HTiv3nNT2SxQ" target="_blank">
                        <i class="icon-youtube">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                        </i>
                    </a>
                    <span class="follow-us">
                        עקבו אחרינו </span>

                </section>

                <div class="search-bar search-bar-container">

                    <form class="navbar-form foody-search-form" role="search" method="get" action="https://foody.co.il">
                        <div class="search-bar d-none d-lg-block">
                            <span class="algolia-autocomplete" style="position: relative; display: inline-block; direction: ltr;"><span class="bmd-form-group"><input name="s" type="text" class="search search-autocomplete foody-input" maxlength="50" placeholder="חפשו מתכון או כתבה…" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="both" aria-expanded="false" aria-owns="algolia-autocomplete-listbox-6" dir="auto" style="position: relative; vertical-align: top;"></span>
                                <pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: Ploni; font-size: 20px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><span class="foody-dropdown-menu" role="listbox" id="algolia-autocomplete-listbox-6" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none; right: auto;">
                                    <div class="foody-dataset-7"></div>
                                </span>
                            </span>
                            <label class="icon" for="textbox">
                                <img src="https://foody.co.il/app/themes/Foody/resources/images/icons/search-gray.png" alt="search-icon">
                            </label>
                        </div>
                    </form>
                </div>
            </section>

        </div>
        <nav class="navbar navbar-expand-lg navbar-light navbar-toggleable-lg " role="navigation">
            <div class="container-fluid foody-navbar-container">
                <div class="site-branding">
                    <div class="logo-container-mobile  d-block d-lg-none">
                        <button class="navbar-toggler custom-logo-link" type="button" data-toggle="collapse" data-target="#foody-navbar-collapse" aria-controls="foody-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                            <!--                                <img class="foody-logo-text" src="-->
                            <!--/foody_logo-with-white.svg">-->
                            <a href="https://foody.co.il">
                                <div class="foody-logo-text hidden"></div>
                            </a>
                            <div class="foody-logo-hamburger hidden"></div>
                            <div class="foody-logo-close"></div>
                        </button>
                    </div>


                </div>

                <button type="button" class="btn btn-default navbar-btn d-block d-lg-none accessibility" data-acsb="trigger" aria-label="פתיחת תפריט נגישות" tabindex="0" role="button">
                    <i class="icon-acces"></i>
                    <div id="accessibility-container"></div>
                </button>
                <div class="related-content-btn-container">
                    <?php if (get_post_type() == "questions" || get_post_type() == "poll") { ?>

                        <span class="related-content-btn ">מתכונים נוספים</span>

                    <?php } ?>
                </div>

                <?php require_once('social-buttons.php');?>

                <div class="navbar-container">
                    <div class="navbar-overlay">
                    </div>
                    <div class="navbar-header">
                        <!--                        <img src="-->
                        <!--" class="top-mobile-menu">-->
                        <div class="signup-purchase-container">
                            <a class="homepage-link" href="https://foody.co.il">
                                <div class="up-arrows">»</div>
                                לעמוד הבית של <span class="foody-name">FOODY</span>
                            </a>
                            <a class="signup-login-link" href="https://foody.co.il/%d7%94%d7%aa%d7%97%d7%91%d7%a8%d7%95%d7%aa/"><span class="singup-text">הרשמו ל-</span><span class="foody-name">FOODY</span>
                                <div class="up-arrows">»</div>
                            </a>


                        </div>
                    </div>

                    <nav id="quadmenu" class="quadmenu-default_theme quadmenu-v2.1.8 quadmenu-align-right quadmenu-divider-hide quadmenu-carets-show quadmenu-background-color quadmenu-mobile-shadow-show quadmenu-dropdown-shadow-show quadmenu-hover-ripple test test quadmenu-touch js" data-template="collapse" data-theme="default_theme" data-unwrap="0" data-width="0" data-selector="" data-breakpoint="768" data-sticky="0" data-sticky-offset="0" style="height: 45%;">
                        <div class="quadmenu-container">
                            <div class="quadmenu-navbar-header">
                                <button type="button" class="quadmenu-navbar-toggle collapsed" data-quadmenu="collapse" data-target="#quadmenu_1" aria-expanded="false" aria-controls="quadmenu">
                                    <span class="icon-bar-container">
                                        <span class="icon-bar icon-bar-top"></span>
                                        <span class="icon-bar icon-bar-middle"></span>
                                        <span class="icon-bar icon-bar-bottom"></span>
                                    </span>
                                </button>
                            </div>
                            <?php if (wp_is_mobile()) {
                                require(get_template_directory() . '/components/mobile_bottom_menu_items.php');
                            }
                            ?>
                        </div>
                    </nav>
                </div>

                <button type="button" id="magnifier_search" class="btn btn-default navbar-btn btn-search d-block d-lg-none" aria-label="חיפוש">

                    <img src="
                    https://foody.co.il/app/themes/Foody/resources/images/icons/search-bar.png" alt="search-bar">

                </button>
                <button type="button" class="btn btn-default navbar-btn btn-search-close hidden d-block d-lg-none" aria-label="חיפוש">

                    <img src="
                    https://foody.co.il/app/themes/Foody/resources/images/icons/search-bar.png" alt="search-bar">

                </button>
            </div>
        </nav>
    </div>
<?php } ?>
<div class="data_res hidden"></div>
<div class="related-recipes-container hidden">
    <div class="close_related_btn">X</div>
</div>
<style>
    .hidden {
        display: none;
        visibility: hidden;
        transition: all 0.2s ease;
    }

    .navbar-container,
    .foody-logo-hamburger,
    .foody-logo-close {
        transition: all 0.9s ease;
    }

    .data_res {
        width: 100%;
        position: fixed;
        top: 50px;
        overflow: scroll;
        z-index: 9999;
        text-align: right;
        color: #000;
        opacity: 1 !important;
        background: #fff;
        padding-top: 10px;
        padding-right: 15px;
        pointer-events: none;
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;

    }

    .in_type{
    font-size: 10px;
    color: red;
    display: block;
    margin-left: 0px;
}
</style>




<script>
    jQuery(document).ready(function() {
    
             
        
        let mobile_menu = jQuery(".navbar-container");
                let hum = jQuery(".foody-logo-hamburger");
                let logo = jQuery(".foody-logo-close");
                let logo_text = jQuery(".foody-logo-text");
                let search_overlay = jQuery(".search-overlay");
                let close = jQuery(".close");

                let related_content = jQuery('.feed-channel-details').html();

                jQuery('.related-recipes-container').append(related_content);
                jQuery('.close_related_btn , .related-content-btn ').click(function() {
                    if (jQuery('.related-recipes-container').hasClass('hidden')) {
                        jQuery('.related-recipes-container').removeClass('hidden');
                        jQuery('.related-recipes-container').addClass("MobileConductor")
                       
                    } else {
                        jQuery('.related-recipes-container').addClass('hidden');
                    }

                });
                logo_text.removeClass("hidden");
                mobile_menu.addClass("hidden");
                hum.addClass("hidden");
                logo.addClass("hidden")

                jQuery(".foody-logo-close , .foody-logo-hamburger").click(function() {

                    if (mobile_menu.hasClass("hidden")) {
                        mobile_menu.removeClass("hidden");
                        hum.addClass("hidden");
                        logo.removeClass("hidden");

                    } else {
                        mobile_menu.addClass("hidden");
                        logo.addClass("hidden");
                        hum.removeClass("hidden");
                    }

                });


                jQuery(window).scroll(function(event) {



                    let scroll = jQuery(window).scrollTop();
                    //console.log("CROLLING---",scroll)
                    if (scroll > 144 && mobile_menu.hasClass("hidden")) {
                        //mobile_menu.removeClass("hidden");
                        jQuery(".foody-logo-close").addClass("hidden");
                        jQuery(".foody-logo-hamburger").removeClass("hidden");
                    }
                    if (scroll < 144 && mobile_menu.hasClass("hidden")) {
                        jQuery(".foody-logo-close").addClass("hidden");
                        jQuery(".foody-logo-hamburger").addClass("hidden");
                        jQuery(".foody-logo-text").removeClass("hidden");
                    }
                });

                jQuery(".close").click(function() {
                    search_overlay.removeClass("open");
                    jQuery(".data_res").addClass("hidden");
                });

                jQuery("#magnifier_search").click(function() {

                    if (search_overlay.hasClass("open")) {
                        search_overlay.removeClass("open");
                    } else {
                        search_overlay.addClass("open");
                    }
                });
                //social-buttons-container
                jQuery(".social-btn-container").click(function() {
                    let social_bar = jQuery(".social-buttons-container");
                    if (social_bar.hasClass("hidden")) {
                        social_bar.removeClass("hidden");
                    } else {
                        social_bar.addClass("hidden")
                    }
                });

                jQuery('#moile_search').on('input', jQuery.debounce(200, function() {
                        jQuery(".overlay-white").html("");
                        let len = jQuery(this).val().length;
                        let the_search = "search=" + jQuery(this).val() + "&action=search_site";
                        if (len > 2) {

                            jQuery.ajax({
                                    type: "POST",
                                    url: "/wp/wp-admin/admin-ajax.php",
                                    data: the_search,

                                    complete: function(data) {
                                        jQuery(".overlay-white").removeClass("hidden");
                                        //console.log(data);
                                        //check if there is actual results
                                        if (data.responseJSON.data.length > 0) {
                                            jQuery.each(data.responseJSON.data, function(i, item) {
                                                    let url = data.responseJSON.data[i].link;
                                                    let subject = data.responseJSON.data[i].name;
                                                    let type_arr = data.responseJSON.data[i].type;
                                                    let type = '';
                                                    switch (type_arr) {
                                                        case 'ingredient':
                                                            type = 'מצרך למתכון' ;
                                                            break;
                                                        case 'post':
                                                            type = 'מתכון' ;
                                                            break;
                                                        }

                                                            let thelink = '<div data-type="'+ type_arr +'" class="sr_res"><a href="' + url + '">' + subject + '</a></div>';
                                                            jQuery(".overlay-white").append(thelink);

                                                            //console.log(data.responseJSON.data[i].name);
                                                    });
                                            }
                                            else {
                                                let thelink = '<div class="sr_res">לא נמצאו תוצאות</div>';
                                                jQuery(".overlay-white").append(thelink);
                                            }

                                        },


                                    });
                            }
                        }));



                }); //end ready
</script>