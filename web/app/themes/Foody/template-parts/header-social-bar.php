<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/6/18
 * Time: 12:35 PM
 */
$show_instagram = !empty($template_args['show_instagram']);
$show_facebook = !empty($template_args['show_facebook']);
$show_youtube = !empty($template_args['show_youtube']);
$instagram_link = foody_get_array_default($template_args,'instagram_link','https://www.instagram.com/foody_israel');
$facebook_link = foody_get_array_default($template_args,'facebook_link','https://www.facebook.com/FoodyIL/');
$youtube_link = foody_get_array_default($template_args,'youtube_link','https://www.youtube.com/channel/UCy_lqFqTpf7HTiv3nNT2SxQ');
?>

<section class="social-icons">

    <?php if ($show_facebook): ?>
        <a href="<?php echo $facebook_link ?>" target="_blank">
            <i class="icon-facebook">
                <span class="path1"></span><span class="path2"></span>
            </i>
        </a>
    <?php endif; ?>

    <?php if ($show_instagram): ?>
        <a href="<?php echo $instagram_link ?>" target="_blank">
            <i class="icon-instagram">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span
                        class="path4"></span>
            </i>
        </a>
    <?php endif; ?>
    <?php if ($show_youtube): ?>
        <a href="<?php echo $youtube_link ?>" target="_blank">
            <i class="icon-youtube">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span
                        class="path4"></span>
            </i>
        </a>
    <?php endif; ?>
    <span class="follow-us">
        <?php echo __('עקבו אחרינו', 'foody') ?>
    </span>

</section>
