<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 3:36 PM
 */

/** @noinspection PhpUndefinedVariableInspection */

/** @var Foody_Post $foody_post */
$foody_post = $template_args['post'];
$post_id = $foody_post->id;

$favorite = [
    'icon' => 'icon-heart',
    'text' => 'הוספה למועדפים'
];

if ($foody_post->favorite) {
    $favorite = [
        'icon' => 'icon-favorite-pressed',
        'text' => 'נשמר במועדפים'
    ];
}

?>

<div class="favorite" data-id="<?php echo $post_id ?>">
    <i class="<?php echo $favorite['icon'] ?>">

    </i>
    <span>
        <?php echo $favorite['text'] ?>
    </span>
</div>
