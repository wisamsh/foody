<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 6:20 PM
 */

$cover_name = '';

if ( isset( $template_args ) ) {
	if ( ! isset( $template_args['image'] ) ) {
		$image = $template_args;
	} else {
		$image = $template_args['image'];
	}
	if ( isset( $template_args['link'] ) ) {
		$link = $template_args['link'];
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
	// add link to cover that was referred by feed channel
    if (isset($_GET) && isset($_GET['referer']) && $_GET['referer']) {
        $link = get_field( 'cover_link' , $_GET['referer']);
        $cover_name = get_field('cover_name', $_GET['referer'] );
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

<div class="host-image">
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
