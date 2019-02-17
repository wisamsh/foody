<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 10:32 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$video_id = $template_args['id'];

?>

<span id="video" style="display: none;" data-video-id="<?php echo $video_id ?>"></span>
<div class="video-container no-print"></div>
<picture class="print-image">
    <?php the_post_thumbnail('foody-main'); ?>
</picture>