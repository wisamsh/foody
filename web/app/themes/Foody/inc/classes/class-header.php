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
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo GOOGLE_TAG_MANAGE_ID?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}