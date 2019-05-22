<?php
/**
 * Template Name: Page Redirect
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */

$tag_manager_id = get_option( 'foody_google_tag_manager_id', GOOGLE_TAG_MANAGER_ID );
?>
<!-- Google Tag Manager -->
<script>(function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
        var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?php echo $tag_manager_id?>');
    window.location.href = '<?php echo $_GET['redirect_to']; ?>';
</script>
<!-- End Google Tag Manager -->