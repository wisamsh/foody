<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/9/18
 * Time: 12:19 PM
 */

$page = get_page_by_path('הרשמה')->ID;
$image_right = get_field('right_image', $page);
$image_left = get_field('left_image', $page);

$profile = $template_args['profile'];

$username = $profile['username'];

?>

<section class="welcome">
    <div>
        <img src="<?php echo $GLOBALS['images_dir'] . 'empty-recipes.svg' ?>" alt="">

    </div>

    <h2>
        <?php echo sprintf(__('%s, נעים להכיר!'), $username) ?>
    </h2>

    <p>
        <?php
        echo __('נרשמת בהצלחה, עכשיו נשאר לארגן את המטבח לארוחה הבאה.', 'foody');
        ?>
    </p>


    <button class="btn btn-primary">

        <a href="<?php echo home_url() ?>"> <?php echo __('עבור לעמוד הראשי', 'foody') ?> </a>
    </button>


</section>

