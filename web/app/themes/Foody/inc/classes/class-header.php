<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 4:17 PM
 */
class Foody_Header
{

    private static $tag_manager_id;

    /**
     * Foody_Header constructor.
     */
    public function __construct()
    {
        self::$tag_manager_id = get_option('foody_google_tag_manager_id', GOOGLE_TAG_MANAGER_ID);
    }

    public function the_socials_bar()
    {
        $show_instagram = get_theme_mod('foody_show_social_instagram');
        $instagram_link = get_theme_mod('foody_social_instagram');
        $show_facebook = get_theme_mod('foody_show_social_facebook');
        $facebook_link = get_theme_mod('foody_social_facebook');
        $show_youtube = get_theme_mod('foody_show_social_youtube');
        $youtube_link = get_theme_mod('foody_social_youtube');
        foody_get_template_part(get_template_directory() . '/template-parts/header-social-bar.php',
            [
                'show_instagram' => $show_instagram,
                'show_facebook' => $show_facebook,
                'show_youtube' => $show_youtube,
                'instagram_link' => $instagram_link,
                'facebook_link' => $facebook_link,
                'youtube_link' => $youtube_link
            ]
        );
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
                w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
                var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '<?php echo self::$tag_manager_id?>');</script>
        <!-- End Google Tag Manager -->
        <?php
    }

    public static function google_tag_manager_iframe()
    {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo self::$tag_manager_id ?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }

    public function accessibility()
    {
        ?>
        <i class="icon-acces"></i>
        <?php
    }

	public function the_logo_mode() {
		$mode          = get_theme_mod( 'foody_logo_mode', false );
		$border_radius = get_theme_mod( 'foody_logo_border_radius', false );
		$logo_class    = '';
		if ( $mode ) {
			$logo_class = 'logo-contain';
		}
		if ( $border_radius ) {
			$logo_class .= ' round-logo';
		}

		echo $logo_class;
	}
}