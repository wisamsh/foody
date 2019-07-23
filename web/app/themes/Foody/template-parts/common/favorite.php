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
$post_id    = $foody_post->id;

$show_text = ! empty( $template_args['show_text'] );

$favorite = [
	'icon'        => 'icon-heart',
	'text'        => 'הוספה למועדפים',
	'mobile_text' => 'מועדפים'
];

if ( $foody_post->favorite ) {
	$favorite = [
		'icon'        => 'icon-favorite-pressed',
		'text'        => 'נשמר במועדפים',
		'mobile_text' => 'מועדפים'
	];
}

?>

<div class="favorite" data-id="<?php echo $post_id ?>">
    <i class="<?php echo $favorite['icon'] ?>">

    </i>
	<?php if ( $show_text ): ?>
        <span>
            <?php echo( wp_is_mobile() ? $favorite['mobile_text'] : $favorite['text'] ); ?>
        </span>
	<?php endif; ?>
</div>
