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
}