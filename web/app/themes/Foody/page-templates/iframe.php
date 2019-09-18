<?php
/**
 * Template Name: promotion-page
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */
get_header();

$destination_id = $_GET['dest'];
$dest_page = get_post($destination_id);
?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="content container" class="site-content" role="main" style="">
                <iframe src="<?php echo $dest_page->post_content;?>" style="width: 100%;
                        height: auto;
                        min-height: 1200px;
                        padding-top: 3%;
                        border: none;"></iframe>
            </div><!-- #content -->
        </div><!-- #primary -->
    </div><!-- #main-content -->

<?php
get_footer();

