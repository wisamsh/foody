<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/1/18
 * Time: 4:25 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Topic $topic */
$topic = $template_args['topic'];

$is_followed = false;
global $wp_session;
if (isset($wp_session["followed_{$topic->get_type()}"])) {

    $followed = $wp_session["followed_{$topic->get_type()}"];

    if ($followed && is_array($followed)) {
        if (in_array($topic->get_id(), $followed)) {
            $is_followed = true;
        }
    }

}

$follow_btn_class = 'btn btn-primary btn-follow';
if ($is_followed) {
    $follow_btn_class .= ' followed';
}


$follow_btn_text = $is_followed ? __('עוקב') : __('עקוב');
?>


<section class="topic-details mx-auto">


    <div class="row">
        <div class="image-container">

            <img src="<?php echo $topic->topic_image() ?>" alt="">

        </div>

        <div class="details col">
            <?php bootstrap_breadcrumb(); ?>

            <h1 class="title">
                <?php echo $topic->topic_title() ?>
            </h1>
            <span class="followers-count">
                <?php echo $topic->get_followers_count(); ?>
            </span>
        </div>
        <div class="follow">
            <button class="<?php echo $follow_btn_class ?>"
                    data-id="<?php echo $topic->get_id() ?>"
                    data-followed="<?php echo $is_followed ? 'true' : 'false' ?>"
                    data-topic="followed_<?php echo $topic->get_type() ?>">

                <i class="icon-Shape"></i>
                <span>
                    <?php echo $follow_btn_text ?>
                </span>
            </button>
            <div class="social d-none d-sm-block">
                <?php
                foody_get_template_part(
                    get_template_directory() . '/template-parts/content-social-actions.php'
                )
                ?>
            </div>
        </div>

        <div class="social d-block d-sm-none">
            <?php
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-social-actions.php'
            )
            ?>
        </div>

    </div>

    <p class="description">
        <?php echo $topic->get_description(); ?>
    </p>
</section>
