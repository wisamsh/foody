<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/9/18
 * Time: 12:19 PM
 */

get_header();

$page = get_page_by_path('הרשמה')->ID;
$image_right = get_field('right_image', $page);
$image_left = get_field('left_image', $page);

$profile = $template_args['profile'];

$username = sanitize_user($profile->displayName, true);

?>

    <div class="content-with-images container-fluid foody-content">

        <div class="row">

            <div class="image-container image-container-right col">
                <img src=" <?php echo $image_right['url'] ?>" alt="<?php echo $image_right['alt'] ?>">
            </div>

            <div class="content col-12 col-sm-4">
                <div class="image-container">
                    <img src="<?php echo $GLOBALS['images_dir'] . 'empty-recipes.svg' ?>" alt="">

                </div>

                <h2>
                    <?php echo sprintf(__('%s, נעים להכיר!'), $username) ?>
                </h2>

                <p>
                    <?php
                    echo __('נרשמת בהצלחה, עכשיו נשאר לארגן את המטבח לארוחה הבאה.
בעוד מספר שניות נחזור לעמוד הראשי!', 'foody');
                    ?>
                </p>

                <a href="<?php echo home_url() ?>">
                    <button class="btn btn-primary">
                        <?php echo __('עבור לעמוד הראשי', 'foody') ?>
                    </button>
                </a>

            </div>

            <div class="image-container image-container-left col">
                <img src=" <?php echo $image_left['url'] ?>" alt="<?php echo $image_left['alt'] ?>">
            </div>

        </div>


    </div>

<?php
get_footer();