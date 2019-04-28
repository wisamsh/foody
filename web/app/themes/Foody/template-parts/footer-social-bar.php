<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/15/18
 * Time: 8:04 PM
 */
$show_instagram = $template_args['show_instagram'];
$show_facebook  = $template_args['show_facebook'];
$show_youtube   = $template_args['show_youtube'];
$instagram_link = $template_args['instagram_link'];
$facebook_link  = $template_args['facebook_link'];
$youtube_link   = $template_args['youtube_link'];
?>

<section class="social-icons">
    <div class="follow-us">
		<?php echo __( 'עיקבו אחרינו', 'foody' ) ?>
    </div>
    <div class="icons">
		<?php if ( $show_facebook ): ?>
            <a href="<?php echo $facebook_link ?>" target="_blank">
                <i class="icon-facebook">
                    <span class="path1"></span><span class="path2"></span>
                </i>
            </a>
		<?php endif; ?>

		<?php if ( $show_instagram ): ?>
            <a href="<?php echo $instagram_link ?>" target="_blank">
                <i class="icon-instagram">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span
                            class="path4"></span>
                </i>
            </a>
		<?php endif; ?>
		<?php if ( $show_youtube ): ?>
            <a href="<?php echo $youtube_link ?>" target="_blank">
                <i class="icon-youtube">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span
                            class="path4"></span>
                </i>
            </a>
		<?php endif; ?>
    </div>

</section>