<?php
/**
 * Template Name: Iframe-courses
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
get_header();
global $post;
$params = ['email' => 'ExtCUserEmail', 'name' => 'ExtCInvoiceTo', 'phone' => 'ExtMobilPhone'];
if (isset($_GET) && !empty($_GET) && isset($_GET['link'])){
    $link = urldecode($_GET['link']);
    if(isset($_GET[$params['email']]) && isset($_GET[$params['name']]) &&  isset($_GET[$params['phone']])){
        $link .= '?'. $params['email'] .'=' . $_GET[$params['email']] . '&'. $params['name'] .'=' . $_GET[$params['name']] . '&'. $params['phone'] .'=' . $_GET[$params['phone']];
    }
}
?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="content container" class="site-content" role="main" style="">
                <iframe src="<?php echo $link;?>" style="width: 100%;
                        height: auto;
                        min-height: 1200px;
                        padding-top: 3%;
                        border: none;"></iframe>
            </div><!-- #content -->
        </div><!-- #primary -->
    </div><!-- #main-content -->
<?php
get_footer();
?>
<!--<script>-->
<!--    function resizeIframe(obj) {-->
<!--        -->
<!--        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';-->
<!--    }-->
<!--</script>-->
<!--scrolling="no" onload="resizeIframe(this)"></iframe>-->

