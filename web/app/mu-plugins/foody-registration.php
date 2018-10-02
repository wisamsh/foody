<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/2/18
 * Time: 11:54 AM
 */


add_action('muplugins_loaded', 'foody_plugin_override');

function foody_plugin_override()
{
    function wsl_render_redirect_to_provider_loading_screen($provider)
    {

        $html = file_get_contents(__DIR__ . '/foody-registration-pages/provider-loading-screen.html');

        echo $html;
        die();
    }


    function _disabled_wsl_render_return_from_provider_loading_screen($provider, $authenticated_url, $redirect_to, $wsl_settings_use_popup)
    {
        ?>
        <!DOCTYPE html>
        <head>
            <meta name="robots" content="NOINDEX, NOFOLLOW">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <title><?php bloginfo('name'); ?></title>
            <style type="text/css">
                html {
                    background: #fff;
                }

                body {
                    background: #fff;
                    color: #444;
                }
            </style>
            <script>
                function init() {
                    document.loginform.submit();
                }
            </script>
        </head>
        <body id="loading-screen" onload="init();">
        <form name="loginform" method="post" action="<?php echo $authenticated_url; ?>">
            <input type="hidden" id="redirect_to" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>">
            <input type="hidden" id="provider" name="provider" value="<?php echo $provider ?>">
            <input type="hidden" id="action" name="action" value="wordpress_social_authenticated">
        </form>
        </body>
        </html>
        <?php
        die();
    }
}