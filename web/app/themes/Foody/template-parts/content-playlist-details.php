<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 6:42 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Playlist $playlist */
$playlist = $template_args['playlist'];

?>

<section class="playlist-details">

    <div class="title-container row gutter-0">
        <i class="icon-heart col-1"></i>

        <h1 class="col">
            <?php echo $playlist->get_playlist_title() ?>
        </h1>
    </div>
    <div class="actions row">
        <button class="btn btn-next col-1 mr-4">
            <i class="icon-heart"></i>
        </button>

        <button class="btn btn-prev col-1">
            <i class="icon-heart"></i>
        </button>

        <span class="count col">
            <?php
            $count = $playlist->num_of_recipes;
            $current = get_query_var('playlist_index', 0) + 1;
            printf('%s/%s', $current, $count);
            ?>
        </span>
    </div>

    <?php
    foody_get_template_part(
        get_template_directory() . '/template-parts/content-social-actions.php'
    )
    ?>
</section>