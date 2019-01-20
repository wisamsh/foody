<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 4:17 PM
 */
class Foody_Header
{


    public function the_socials_bar()
    {

        foody_get_template_part(get_template_directory() . '/template-parts/header-social-bar.php');
    }

    public static function facebook_init()
    {
        ?>

        <script>
            window.fbAsyncInit = function () {
                FB.init({
                    appId: '<?php echo FACEBOOK_APP_ID?>',
                    cookie: true,
                    xfbml: true,
                    version: '<?php echo FACEBOOK_API_VERSION ?>'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <?php
    }

    public static function google_tag_manager()
    {
        ?>
        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(), event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '<?php echo GOOGLE_TAG_MANAGE_ID?>');</script>
        <!-- End Google Tag Manager -->
        <?php
    }

    public static function google_tag_manager_iframe()
    {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo GOOGLE_TAG_MANAGE_ID ?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }

    public function accessibility()
    {
        ?>
        <svg width="20px" height="22px" viewBox="0 0 20 22" version="1.1" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink">
            <!-- Generator: sketchtool 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
            <title>693DD928-8D13-4224-83FC-65FC57F11EA2</title>
            <desc>Created with sketchtool.</desc>
            <defs></defs>
            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="HomePage--mobile" transform="translate(-324.000000, -95.000000)" fill="#ED3D48"
                   fill-rule="nonzero">
                    <g id="747471" transform="translate(324.000000, 95.000000)">
                        <path d="M17.9092308,18.3228512 L16.8837278,13.7006709 C16.6577071,12.6860797 15.7127071,11.9496855 14.6367456,11.9496855 L10.3123964,11.9496855 C9.85760355,11.9496855 9.49254438,11.652956 9.44448225,11.2441509 L9.25627219,9.64360587 L13.6982249,9.64360587 L13.6982249,7.96645702 L9.05900888,7.96645702 L8.69088757,4.83614256 C9.65008876,4.45534591 10.3254438,3.55870021 10.3254438,2.51572327 C10.3254438,1.12855346 9.13094675,0 7.66272189,0 C6.19449704,0 5,1.12855346 5,2.51572327 C5,3.65790356 5.81013314,4.62415094 6.9158284,4.93002096 L7.6802071,11.429392 C7.82758876,12.6821384 8.95920118,13.6268344 10.3124408,13.6268344 L14.6367899,13.6268344 C14.8742604,13.6268344 15.0934024,13.807086 15.1465237,14.0454088 L16.4675888,20 L20,20 L20,18.3228512 L17.9092308,18.3228512 Z M7.66272189,3.35429769 C7.17331361,3.35429769 6.77514793,2.97811321 6.77514793,2.51572327 C6.77514793,2.05333333 7.17331361,1.67714885 7.66272189,1.67714885 C8.15213018,1.67714885 8.55029586,2.05333333 8.55029586,2.51572327 C8.55029586,2.97811321 8.15213018,3.35429769 7.66272189,3.35429769 Z"
                              id="Shape"></path>
                        <path d="M7.01339515,20.2477933 C4.1069745,20.2477933 1.74245842,17.8700488 1.74245842,14.9473681 C1.74245842,12.3742964 3.57530687,10.2239007 5.99697559,9.74624918 L5.80404188,8 C2.51114396,8.57796538 0,11.4731803 0,14.9473681 C0,18.8361718 3.14622649,22 7.01339515,22 C10.6750844,22 13.689886,19.1633526 14,15.5606404 L12.2488728,15.5606404 C11.9455544,18.1954336 9.71350872,20.2477933 7.01339515,20.2477933 Z"
                              id="Shape"></path>
                    </g>
                </g>
            </g>
        </svg>
        <style>
            body.contrast svg g{
                stroke:white;
            }
        </style>
        <?php


    }
}