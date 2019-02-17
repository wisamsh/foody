<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 11:52 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$playlist_item = $template_args;
?>


<div class="image-container">

    <img src="<?php echo $playlist_item['image'] ?>" alt="">

    <div class="playlist-count">
        <i class="icon-timeplay"></i>

        <div class="count">
            <?php echo sprintf('%s מתכונים', $playlist_item['count']) ?>
        </div>

    </div>
</div>
