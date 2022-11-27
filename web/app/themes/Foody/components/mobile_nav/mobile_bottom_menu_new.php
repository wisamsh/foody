<?php
$domain_name = $_SERVER['HTTP_HOST'];
?>
<?php if (wp_is_mobile()) { ?>

    <div id="search_overlay" class="search-overlay floating-mobile-header d-lg-none">

        <div class="input-container">
            <span class="algolia-autocomplete" style="position: relative; display: inline-block; direction: ltr;">
                <span class="algolia-autocomplete" style="position: relative; display: inline-block; direction: ltr;">
                    <span class="bmd-form-group">
                        <input type="search" placeholder="הקלד מילת חיפוש כאן..." id="moile_search" class="" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="both" aria-expanded="false" aria-owns="algolia-autocomplete-listbox-10" dir="auto" style="direction:rtl;text-align:right;position: relative; vertical-align: top;"></span>

                    <span class="close">×</span>
        </div>
        <div class="overlay-white"> </div>
    </div>

    </div>




    <div class="sticky_bottom_header no-print">
        <div class="socials d-none d-lg-block">

            <section class="header-top-container d-none d-lg-flex">

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
                    <div class="logo-container-mobile d-block d-lg-none">
                        <button class="navbar-toggler custom-logo-link" type="button" data-toggle="collapse" data-target="#foody-navbar-collapse" aria-controls="foody-navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
                            <!-- <img class="foody-logo-text" src="-->
                            <!--/foody_logo-with-white.svg">-->

                            <!--
                            <div class="foody-logo-text hidden">
                                <a href="/" class="togo"> </a>

                            </div>
                    -->
                            <div class="foody-logo-hamburger"></div>
                            <div class="foody-logo-close"></div>
                        </button>

                    </div>


                </div>

                <button type="button" class="btn btn-default navbar-btn d-block d-lg-none accessibility" data-acsb="trigger" aria-label="פתיחת תפריט נגישות" tabindex="0" role="button">
                    <i class="icon-acces"></i>
                    <div id="accessibility-container"></div>
                </button>
                <div class="related-content-btn-container">
                    <?php
                    require(get_template_directory() . '/components/mobile_nav/related_recipes.php');
                    ?>
                </div>

                <?php require(get_template_directory() . '/components/social-buttons.php'); ?>


                <div class="navbar-container_free">
                    <div class="MM_Foody_head">
                        <div class="logoSVG">
                            <?php require(get_template_directory() . '/components/svg-logo.php'); ?>

                        </div>

                        <?php require(get_template_directory() . '/components/mobile_nav/register_login.php'); ?>

                    </div>

                    <nav id="Foody_Mobile_nav" class="mobile_footer_main_navigation">
                        <div class="Foody_Mobile_nav-container">

                            <?php if (wp_is_mobile()) {
                                require(get_template_directory() . '/components/mobile_nav/mobile_bottom_menu_items_new.php');
                            }
                            ?>
                        </div>
                    </nav>

                </div>
                <button type="button" id="magnifier_search" class="btn btn-default navbar-btn btn-search d-block d-lg-none" aria-label="חיפוש">

                    <img src="https://foody.co.il/app/themes/Foody/resources/images/icons/search-bar.png" alt="search-bar">

                </button>
                <button type="button" class="btn btn-default navbar-btn btn-search-close hidden d-block d-lg-none" aria-label="חיפוש">

                    <img src="https://foody.co.il/app/themes/Foody/resources/images/icons/search-bar.png" alt="search-bar">

                </button>
            </div>
        </nav>
    </div>
<?php } ?>
<div class="data_res hidden"></div>

<style>
    @media (max-width: 768px) {

        .filter-btn {
            bottom: 100px !important;

        }

        .show-recipes {
            top: -61px !important;
        }

    }

    .hidden {
        max-height: 0;
        display: none;
        visibility: hidden;

    }

    .unhidden_w {

        display: block;
        visibility: visible;

    }

    #masthead {
        display: none;
    }

    .regist a {

        font-size: 25px;
        width: 100%;
        font-weight: bold;
    }

    .regist {

        text-align: center;
        width: 100%;

    }

    .unhide {
        display: block;
        visibility: hidden;

    }

    .logoSVG {

        margin: 0 auto;
        width: 100px;
        margin-top: 70px;

    }

    .quadmenu-navbar-nav .menu_item_top :last-child>.plus_btn {
        display: none !important;
    }


    .menu_item_top .sub-menu .menu_item_top .plus_btn {
        display: none;
    }

    .menu_item_top {
        margin-top: 20px;
    }

    .menu_item_top a {
        font-size: 26px;
        font-weight: bold;

    }

    .navbar-container_free {
        width: 100%;
        left: 0px;
        height: 100%;
        bottom: 64px !important;
        position: fixed;
        padding: 10px;
        overflow: auto;
        background: #d9ebf9;


    }



    .quadmenu-navbar-nav,
    .sub-menu {
        list-style: none !important;
    }

    .plus_btn {
        font-size: 57px !important;
        position: absolute;
        left: 47px;
        margin-top: -25px;
        color: #7d8185a8;
        font-weight: normal !important;
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

    .in_type {
        font-size: 10px;
        color: red;
        display: block;
        margin-left: 0px;
    }

    .open {
        opacity: 1;

        width: 100vw;

    }

    .sub-menu .menu_item_top a {
        font-weight: normal !important;
        font-size: 22px;
    }

    #quadmenu_new {
        margin-top: 90px;
    }

    .plus_btn:last-child {
        content: '';
    }


    .Conductor_overlay {
        width: 100%;
        height: 70%;
        background: #ffffffdb;
        position: fixed;
        top: 0;
        left: 0;
    }

    .related-recipes-container {
        width: 100%;
        position: fixed;
        min-height: 400px;
        background: #fff;
        left: 0;
        right: 0;
        bottom: 64px;
        padding: 10px;
        overflow: auto;
        transition: all 0.8s ease;
        direction: rtl !important;
        box-shadow: rgba(0, 0, 0, 0.06) 0px 2px 4px 0px inset;
    }

    .related_title {
        width: 80%;
        text-align: right;
        margin-bottom: 10px;
        font-weight: bold;
        color: #579fba;
        font-size: 19px;
        position: relative;
    }

    .close_related_btn {
        position: absolute;
        left: 10px;
        top: 7px;
        color: #579fba;
        font-size: 23px;
        /* border: solid 1px #6d9fba; */
        /* border-radius: 50%; */
        width: 30px;
        height: 30px;
        text-align: center;
        justify-content: center;
        font-weight: bold;
    }

    .related_img {
        object-fit: cover !important;
        width: 171px !important;
        height: 115px !important;
        object-fit: cover !important;

    }

    .colish {
        width: 49%;
        display: flex;
        text-align: center;
        padding: 5px;
    }

    #masthead {
        display: none;
    }

    .related_spn {
        font-size: 12.5px;
        text-align: center;
        font-weight: 700;
    }

    .sr_res {
        width: 100%;
        border-bottom: #ddd;
        text-align: right;
        background: #ffffff;
        padding-right: 20px;
        padding-bottom: 14px;
        padding-top: 8px;
        padding-left: 10px;
        border-bottom: 1px solid #dddddd45;
        position: relative;
    }

    .sr_res:last-child {
        height: 100%;
    }

    .sr_res a {
        font-size: 22px;
        font-weight: 400;
    }

    .overlay-white {
        opacity: 1 !important;
        background: #fff !important;
        height: calc(100vh - 60px);
        background-attachment: fixed;
        overflow: scroll;
        margin-top: -2px;
    }

    .search_list {
        list-style: none !important;
        background: #fff;
    }

    .socials,
    .follow {
        display: none;
    }

    footer.site-footer .footer-pages-mobile ul {

        margin: 0 auto !important;
        margin-bottom: 10px !important;
        padding: 0 5px;
        list-style-type: none;
        text-align: center;
    }


    footer.site-footer {
        padding-bottom: 101px;
    }

    .welcome_div {
        font-size: 20px;
        font-weight: bold;
    }


    .welcome_div a {
        font-size: 20px !important;
        font-weight: bold !important;
    }


    .type_of_search_res {

        font-size: 12px;
        color: #68a9c1;
        display: block;
        position: absolute;
        left: 6px;
    }

    .mobile-filter .show-recipes-container button {
        height: 43px;
        font-size: 23px;
        color: #fff;
        border-radius: 50%;
        width: 43px;
        text-align: center;
        /* padding: 4px; */
        overflow: hidden;
        font-weight: bold;
        background: #59a0bb !important;
    }

    .loader {
        width: 100%;
        text-align: center;
        margin-top: 200px;
        position: relative;
    }

    .loader span {
        color: #589fba;
        display: block;
        margin-top: 10px;
    }

    <?php
    $domain_name = $_SERVER['HTTP_HOST'];

    switch ($domain_name) {
        case  'miricohen.foody.co.il':
            echo (".foody-logo-text{background-image: url('https://d3o5sihylz93ps.cloudfront.net/wp-content/uploads/sites/6/2021/07/06164126/cropped-logo-gold.png') !important;} ");
            break;
        case  'www.miricohen.foody.co.il':
            echo (".foody-logo-text{background-image: url('https://d3o5sihylz93ps.cloudfront.net/wp-content/uploads/sites/6/2021/07/06164126/cropped-logo-gold.png') !important;} ");
            break;


        case  'danielamit.foody.co.il':
            echo (".foody-logo-text{background-image: url('https://d3o5sihylz93ps.cloudfront.net/wp-content/uploads/sites/5/2021/07/05123510/cropped-Asset-4.png') !important;} ");
            break;
        case  'www.danielamit.foody.co.il':
            echo (".foody-logo-text{background-image: url('https://d3o5sihylz93ps.cloudfront.net/wp-content/uploads/sites/5/2021/07/05123510/cropped-Asset-4.png') !important;} ");
            break;


        case  'carine.co.il':
            echo (".foody-logo-text{background-image: url('https://d3o5sihylz93ps.cloudfront.net/wp-content/uploads/sites/2/2019/06/25063844/unnamed.png') !important;} ");
            break;
        case  'www.carine.co.il':
            echo (".foody-logo-text{background-image: url('https://d3o5sihylz93ps.cloudfront.net/wp-content/uploads/sites/2/2019/06/25063844/unnamed.png') !important;} ");
            break;

        default:
            echo (".foody-logo-text{background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI1LjAuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCA2MiA2MCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNjIgNjA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojRTYzOTJCO30KCS5zdDF7ZmlsbDojRkZGRkZGO30KPC9zdHlsZT4KPHBhdGggY2xhc3M9InN0MCIgZD0iTTU4LjcsMTYuMmMtMC4yLTAuNC0wLjctMC42LTEuMS0wLjNDNTMuMSwxOC40LDQ3LjMsMTcuMiw0NCwxM2MtMC4zLTAuNC0wLjYtMC45LTAuOS0xLjQKCWMtMC4yLTAuNC0wLjctMC41LTEtMC4zYy01LjMsMi44LTEyLDEuNy0xNi4yLTNjLTItMi4yLTMuMS01LTMuMy03LjhjMC0wLjQtMC40LTAuNi0wLjctMC41QzkuMywzLjksMC4yLDE1LjcsMC4zLDI5LjUKCWMwLjEsMTcsMTQsMzAuNiwzMC45LDMwLjVzMzAuNi0xMy45LDMwLjUtMzAuOUM2MS42LDI0LjUsNjAuNiwyMC4xLDU4LjcsMTYuMnoiLz4KPGcgaWQ9IkxheWVyXzJfMV8iPgoJPGcgaWQ9IkxheWVyXzEtMiI+CgkJPHBhdGggY2xhc3M9InN0MSIgZD0iTTQ4LjYsMzEuMWwtMS44LTkuNmgtMS45bDIuOCwxMS45djkuMmgxLjl2LTkuMmwyLjgtMTEuOWgtMS45TDQ4LjYsMzEuMUw0OC42LDMxLjF6IE0zOC43LDIzaDEKCQkJYzAuNS0wLjEsMC45LDAuMSwxLjMsMC4zYzAuMywwLjQsMC40LDAuOCwwLjQsMS4zdjE0LjdjMC4xLDAuNS0wLjEsMC45LTAuNCwxLjNjLTAuNCwwLjMtMC44LDAuNC0xLjMsMC4zaC0xVjIzeiBNNDAuMSw0Mi41CgkJCWMxLDAuMSwyLTAuMiwyLjctMC45YzAuNS0wLjcsMC43LTEuNiwwLjYtMi41VjI0LjljMC4xLTAuOS0wLjItMS44LTAuNi0yLjVjLTAuNy0wLjctMS43LTEtMi43LTAuOWgtMy4zdjIxLjFMNDAuMSw0Mi41egoJCQkgTTMwLjMsMjIuOWMxLDAsMS41LDAuNywxLjUsMnYxNC4yYzAsMS4zLTAuNSwyLTEuNSwycy0xLjUtMC43LTEuNS0yVjI0LjlDMjguOCwyMy42LDI5LjMsMjIuOSwzMC4zLDIyLjkgTTMwLjMsNDIuOAoJCQljMi4zLDAsMy40LTEuMiwzLjQtMy42VjI0LjljMC0yLjQtMS4yLTMuNi0zLjQtMy42Yy0yLjMsMC0zLjQsMS4yLTMuNCwzLjZ2MTQuM0MyNi45LDQxLjYsMjgsNDIuOCwzMC4zLDQyLjggTTIwLjUsMjIuOQoJCQljMSwwLDEuNSwwLjcsMS41LDJ2MTQuMmMwLDEuMy0wLjUsMi0xLjUsMnMtMS41LTAuNy0xLjUtMlYyNC45QzE5LDIzLjYsMTkuNSwyMi45LDIwLjUsMjIuOSBNMjAuNSw0Mi44YzIuMywwLDMuNC0xLjIsMy40LTMuNgoJCQlWMjQuOWMwLTIuNC0xLjEtMy42LTMuNC0zLjZzLTMuNCwxLjItMy40LDMuNnYxNC4zQzE3LjEsNDEuNiwxOC4yLDQyLjgsMjAuNSw0Mi44IE0xMS40LDQyLjZWMzIuNWgzdi0xLjZoLTN2LTcuN2gzLjJ2LTEuNkg5LjUKCQkJdjIxLjFIMTEuNHoiLz4KCTwvZz4KPC9nPgo8L3N2Zz4K') !important;} ");
    }
    //foody-logo-text 
    ?>
</style>




<script>
    //jQuery(document).ready(function() {
    function swapme(id) {

        if (jQuery("#" + id).text() == "+") {
            jQuery("#" + id).text("-");
        } else {
            jQuery("#" + id).text("+");
        }
    }

    jQuery(".logoSVG").click(function() {

        window.location.href = "/";

    });

    jQuery(".sub-menu").slideToggle();

    jQuery(".plus_btn").click(function() {
        //sub-menu

        jQuery(this).parent().find(".sub-menu").slideToggle();

    });

    let mobile_menu = jQuery(".navbar-container_free");
    let hum = jQuery(".foody-logo-hamburger");
    let logo = jQuery(".foody-logo-close");
    let logo_text = jQuery(".foody-logo-text");
    let search_overlay = jQuery("#search_overlay");
    let close = jQuery(".close");

    let related_content = jQuery('.feed-channel-details').html();

    logo_text.removeClass("hidden");
    mobile_menu.addClass("hidden");
    hum.addClass("hidden");
    logo.addClass("hidden")

    jQuery('.related-recipes-container').append(related_content);
    jQuery('.close_related_btn , .related-content-btn ').click(function() {
        jQuery('.close').trigger('click');
        if (jQuery('.related-recipes-container').hasClass('hidden')) {
            jQuery('.related-recipes-container').removeClass('hidden');
            jQuery('.Conductor_overlay').removeClass("hidden");
            mobile_menu.addClass("hidden");
            logo.addClass("hidden");
            hum.removeClass("hidden");

        } else {
            jQuery('.related-recipes-container').addClass('hidden');
            jQuery('.Conductor_overlay').addClass("hidden");
            mobile_menu.addClass("hidden");
            logo.addClass("hidden");
            hum.removeClass("hidden");

        }

    });

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

    //jQuery(window).scroll(function(event) {

        //let scroll = jQuery(window).scrollTop();
        //console.log("CROLLING---",scroll)
        //if (scroll > 20 && mobile_menu.hasClass("hidden")) {
            //mobile_menu.removeClass("hidden");
            //jQuery(".foody-logo-close").addClass("hidden");
            //jQuery(".foody-logo-hamburger").removeClass("hidden");

       // }
       // if (scroll < 20 && mobile_menu.hasClass("hidden")) {
            // jQuery(".foody-logo-close").addClass("hidden");
            // jQuery(".foody-logo-hamburger").addClass("hidden");
            // jQuery(".foody-logo-text").removeClass("hidden");
       // }
    //});

    jQuery(".close").click(function() {

        search_overlay.removeClass("open");
        jQuery(".data_res").addClass("hidden");
        mobile_menu.addClass("hidden");
        logo.addClass("hidden");
        hum.removeClass("hidden");
        jQuery(".filter-btn").removeClass('hidden')

    });

    jQuery("#magnifier_search , .btn-search-close").click(function() {

        jQuery(".filter-btn").addClass('hidden')
        mobile_menu.addClass("hidden");
        logo.addClass("hidden");
        hum.removeClass("hidden");
        search_overlay.show();
        jQuery(".Conductor_overlay , .related-recipes-container").addClass("hidden");

    });

    //social-buttons-container
    jQuery(".social-btn-container").click(function() {
        let social_bar = jQuery(".mobile_social");
        social_bar.slideToggle();
        // if (social_bar.hasClass("hidden")) {
        // social_bar.removeClass("hidden");
        // } else {
        social_bar.addClass("hidden")
        mobile_menu.addClass("hidden");
        logo.addClass("hidden");
        hum.removeClass("hidden");
        //}
    });

    jQuery('#moile_search').on('input', jQuery.debounce(200, function() {
        jQuery(".overlay-white").html("");
        let len = jQuery(this).val().length;
        let the_search = "search=" + jQuery(this).val() + "&action=search_site";
        if (len > 2) {

            let Load = "<div class='loader'><img src='https://foody-media.s3.eu-west-1.amazonaws.com/w_images/loading.gif'/><span>מחפש בפודי...</span></div>";
            jQuery(".overlay-white").html(Load);
            let thelink = "";
            jQuery.ajax({
                type: "POST",
                url: "/wp/wp-admin/admin-ajax.php",
                data: the_search,

                complete: function(data) {
                    jQuery(".overlay-white").removeClass("hidden");
                    //console.log(data);
                    //check if there is actual results

                    if (data.responseJSON.data.length > 0) {
                        data.responseJSON.data.reverse();
                        jQuery.each(data.responseJSON.data, function(i, item) {

                            let url = data.responseJSON.data[i].link;
                            let subject = data.responseJSON.data[i].name;
                            let type_arr = data.responseJSON.data[i].type;
                            let type = '';
                            switch (type_arr) {
                                case 'ingredient':
                                    type = 'מצרך למתכון';
                                    break;
                                case 'post':
                                    type = 'מתכון';
                                    break;
                                case 'feed_channel':
                                    type = 'מתחם';
                                    break;

                            }

                            thelink += '<div data-type="' + type_arr + '" class="sr_res"><a href="' + url + '">' + subject + '</a> ';

                            thelink += '<span class="type_of_search_res">' + type + '</span></div>';

                        });

                    } else {
                        thelink = '<div class="sr_res">לא נמצאו תוצאות</div>';

                    }
                    jQuery(".overlay-white").html("");
                    jQuery(".overlay-white").append(thelink);

                },

            });

        }
    }));



    // }); //end ready
</script>