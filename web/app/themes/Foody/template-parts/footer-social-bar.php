<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/15/18
 * Time: 8:04 PM
 */
$show_instagram = isset( $template_args['show_instagram'] ) ? $template_args['show_instagram'] : true;
$show_facebook  = isset( $template_args['show_facebook'] ) ? $template_args['show_facebook'] : true;
$show_youtube   = isset( $template_args['show_youtube'] ) ? $template_args['show_youtube'] : true;
$instagram_link = isset( $template_args['instagram_link'] ) ? $template_args['instagram_link'] : 'https://www.instagram.com/foody_israel';
$facebook_link  = isset( $template_args['facebook_link'] ) ? $template_args['facebook_link'] : 'https://www.facebook.com/FoodyIL/';
$youtube_link   = isset( $template_args['youtube_link'] ) ? $template_args['youtube_link'] : 'https://www.youtube.com/channel/UCy_lqFqTpf7HTiv3nNT2SxQ';
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