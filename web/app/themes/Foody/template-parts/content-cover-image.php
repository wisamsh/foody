<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 6:20 PM
 */
global $post;
$cover_name = '';
$is_register_page = false;

if ( isset( $template_args ) ) {
	if ( ! isset( $template_args['image'] ) ) {
		$image = $template_args;
	} else {
		$image = $template_args['image'];
	}
	if ( isset( $template_args['link'] ) ) {
		$link = $template_args['link'];
	}
	if ( isset($template_args['type'] ) ) {
	   $cover_name = get_field('cover_name' );
    }
	if(isset($template_args['is_register'])){
	    $is_register_page = $template_args['is_register'];
    }
}



if ( empty( $image ) ) {
	$image = get_header_image();
	$alt="";
} else {
    $alt = $image['alt'];
    $image = $image['url'];
}

if ( empty( $link ) ) {
	$link = get_field( 'cover_link' );
    $feed_area_id = !empty($post->id) ? get_field('recipe_channel', $post->id) : get_field('recipe_channel');
    $feed_area_id = is_category() ? get_field('recipe_channel', get_queried_object()) : $feed_area_id;

    // add link to cover that was referred by feed channel
    if ((isset($_GET) && isset($_GET['referer']) && $_GET['referer']) || $feed_area_id) {
        $recipe_referer = isset($_GET) && isset($_GET['referer'] ) && $_GET['referer'] ? $_GET['referer'] : $feed_area_id;
        $link = get_field( 'cover_link' , $recipe_referer);
        $cover_name = get_field('cover_name', $recipe_referer );
    }
}
if ( ! empty( $link ) ) {
	$a = '<a href="' . $link['url'] . '" target="' . $link['target'] . '">';
}

$mobile_image = $image;

if ( isset( $template_args['mobile_image'] ) ) {
	/** @noinspection PhpUndefinedVariableInspection */
	$mobile_image = $template_args['mobile_image'];
	if ( ! is_string( $mobile_image ) ) {
		$mobile_image = $mobile_image['url'];
	}
}

?>
<?php
$is_recipe = isset($post->post_type) && $post->post_type == 'foody_recipe';

if ( in_category( 'עוגות', get_the_ID() ) ){  ?>
    <div class="cover-image old <?php echo $is_register_page ? 'register-page' : ''?> <?php echo $is_recipe ? 'no-print' : ''?>">
<?php } else { ?>
    <div class="cover-image  <?php echo $is_register_page ? 'register-page' : ''?> <?php echo $is_recipe ? 'no-print' : ''?>">
<?php } ?>



	<?php if ( isset( $a ) ) {
		echo $a;
	} ?>


    <picture>
        <source media="(min-width: 800px)" srcset="<?php echo $image; ?>"
        ">
        <source media="(max-width: 799px)"
                srcset="<?php echo $mobile_image; ?>"
        ">
        <img src="<?php echo $image ?>" alt="<?php echo $alt;?>" data-name="<?php echo $cover_name;?>">
    </picture>


    <!--    <img src="--><?php //echo $image ?><!--" alt="">-->

	<?php if ( isset( $a ) ) {
		echo '</a>';
	} ?>

</div>
