<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 4:17 PM
 */
class Foody_Header {

	private static $tag_manager_id;

	/**
	 * Foody_Header constructor.
	 */
	public function __construct() {
		self::$tag_manager_id = get_option( 'foody_google_tag_manager_id', GOOGLE_TAG_MANAGER_ID );
	}

	public function the_socials_bar() {
		$show_instagram = get_theme_mod( 'foody_show_social_instagram' );
		$instagram_link = get_theme_mod( 'foody_social_instagram' );
		$show_facebook  = get_theme_mod( 'foody_show_social_facebook' );
		$facebook_link  = get_theme_mod( 'foody_social_facebook' );
		$show_youtube   = get_theme_mod( 'foody_show_social_youtube' );
		$youtube_link   = get_theme_mod( 'foody_social_youtube' );
		foody_get_template_part( get_template_directory() . '/template-parts/header-social-bar.php',
			[
				'show_instagram' => $show_instagram,
				'show_facebook'  => $show_facebook,
				'show_youtube'   => $show_youtube,
				'instagram_link' => $instagram_link,
				'facebook_link'  => $facebook_link,
				'youtube_link'   => $youtube_link
			]
		);
	}

	public static function facebook_init() {
		?>

        <script async defer>
            setTimeout(() => {
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
            });
        </script>

		<?php
	}

	public static function google_tag_manager() {
		?>
        <!-- Google Tag Manager -->
        <script async defer>(function (w, d, s, l, i) {
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

	public static function google_tag_manager_iframe() {
		?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe aria-hidden="true"
                    src="https://www.googletagmanager.com/ns.html?id=<?php echo self::$tag_manager_id ?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
		<?php
	}

	public function accessibility() {
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

	public function the_logo_nav_mode() {
		$mode       = get_theme_mod( 'foody_logo_mode', false );
		$logo_class = '';
		if ( $mode ) {
			$logo_class = 'nav-bar-contain-logo';
		}

		echo $logo_class;
	}

	public function the_foody_collaboration( $desktop ) {
		$collab_text        = get_theme_mod( 'foody_collaboration_text', false );
		$show_collaboration = get_theme_mod( 'show_foody_collaboration_text', false );
		$foody_url          = function_exists( 'foody_get_main_site_url' ) ? foody_get_main_site_url() : get_home_url();

		if ( $show_collaboration ) {
			if ( $desktop ) {
				echo '<span class="foody-collaboration-desktop foody-collaboration">';
			} else {
				echo '<div class="foody-collaboration d-block d-lg-none">';
			}
            $current_blog_id = get_current_blog_id();
            if($current_blog_id == 2){
                echo '<a href="' . $foody_url .'?utm_source=Carine%20Site&utm_medium=Logo&utm_campaign=Foody%20Logo'. '" target="_blank">';
            }
            else {
                echo '<a href="' . $foody_url . '" target="_blank">';
            }
			echo '<span>' . $collab_text . '</span>';
			echo '<img src="' . $GLOBALS['images_dir'] . 'foody-logo.svg" alt="Foody">';
			echo '</a> ';
			if ( $desktop ) {
				echo '</span>';
			} else {
				echo '</div>';
			}
		}
	}

	public static function whatsapp( $ext_classes = [] ) {
		$phone_number = get_option( 'whatsapp_phone_number' );
		$url          = "https://api.whatsapp.com/send?phone=$phone_number";
		if ( ! wp_is_mobile() ) {
			$url = "https://web.whatsapp.com/send?phone=$phone_number";
		}

		$show  = get_option( 'whatsapp_phone_number_toggle', false );

		$classes = $ext_classes;
		if ( ! $show ) {
			$classes[] = 'invisible';
		}


		foody_get_template_part( get_template_directory() . '/template-parts/whatsapp-business.php', [
			'url'     => $url,
			'classes' => $classes
		] );
	}

	public static function getPrintHeader($mobile = false){
	    $title_text = __('עוד מתכון מ-');
//	    $classes = $mobile ? 'print-header print-mobile' : 'print-header print-desktop';
        $classes = 'print-header print-desktop';
	    $title_element = '<h1 class="print-header-text print">'.$title_text.'</h1>';
	    $image_element = foody_custom_logo_link(true);

	    echo '<div class="'. $classes .'">'.$title_element.$image_element.'</div>';
    }
}