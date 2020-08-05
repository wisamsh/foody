<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 3:36 PM
 */

/** @noinspection PhpUndefinedVariableInspection */

/** @var Foody_Post $foody_post */
global $post;
$foody_post = $template_args['post'];
$post_id    = $foody_post->id;
if(!is_null($post)) {
    $is_recipe = $post->post_type == 'foody_recipe';
}
else{
    $is_recipe = false;
}

$show_text = ! empty( $template_args['show_text'] );

$favorite = [
	'icon'        => 'icon-heart',
	'text'        => 'הוספה למועדפים',
	'mobile_text' => 'שמרו'
];

if ( $foody_post->favorite ) {
	$favorite = [
		'icon'        => 'icon-favorite-pressed',
		'text'        => 'נשמר במועדפים',
		'mobile_text' => 'נשמר'
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
<?php if(!wp_is_mobile() && $is_recipe) { ?>

    <div class="kosher-sign">
        <?php echo __('כשר'); ?>
    </div>
<?php } ?>
