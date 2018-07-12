<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 11:52 AM
 */

$playlist_item = $template_args;
?>


<li class="related-item playlist">

    <div class="image-container">

        <img src="<?php echo $playlist_item['image'] ?>" alt="">

        <div class="playlist-count">
            <i class="icon-play"></i>

            <div class="count">
                <?php echo sprintf('%s מתכונים', $playlist_item['count']) ?>
            </div>

        </div>
    </div>
    <div class="playlist-details">
        <h3 class="post-title">
            <?php echo $playlist_item['title'] ?>
        </h3>

        <a class="author-name" href="<?php echo $playlist_item['author']['link'] ?>">
            <?php echo $playlist_item['author']['name'] ?>
        </a>

        <span class="view-count">
            <?php echo $playlist_item['view_count'] ?>
        </span>
    </div>

</li>
