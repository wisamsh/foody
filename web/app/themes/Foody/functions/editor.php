<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 4:27 PM
 */

/*
* Callback function to filter the MCE settings
*/

function foody_mce_before_init_insert_formats( $init_array ) {

// Define the style_formats array

	$style_formats = array(
		/*
		* Each array child is a format with it's own settings
		* Notice that each array has title, block, classes, and wrapper arguments
		* Title is the label which will be visible in Formats menu
		* Block defines whether it is a span, div, selector, or inline style
		* Classes allows you to define CSS classes
		* Wrapper whether or not to add a new block-level element around any selected elements
		*/
		array(
			'title'   => 'Full Width Image',
			'block'   => 'span',
			'classes' => 'image-full-width',
			'wrapper' => false,

		)
	);
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['formats'] = json_encode( $style_formats );

	return $init_array;

}

// Attach callback to 'tiny_mce_before_init'
add_filter( 'tiny_mce_before_init', 'foody_mce_before_init_insert_formats' );

function custom_meta_box_markup() {
	?>
    <h2 style="font-size: x-large;">

		<?php echo get_the_ID() ?>

    </h2>
	<?php
}

function add_custom_meta_box() {
	add_meta_box( "recipe-id", "מזהה מתכון", "custom_meta_box_markup", "foody_recipe", "side", "high", null );
}

add_action( "add_meta_boxes", "add_custom_meta_box" );

// remove inline width from figure tags
add_filter( 'img_caption_shortcode_width', '__return_false' );


function add_image_class( $class ) {
	$class .= ' foody-image';

	return $class;
}

add_filter( 'get_image_tag_class', 'add_image_class' );

function wrap_content_images( $content ) {

	$content  = mb_convert_encoding( $content, 'HTML-ENTITIES', "UTF-8" );
	$document = new DOMDocument();
	libxml_use_internal_errors( true );
	$document->loadHTML( utf8_decode( $content ) );

	$imgs = $document->getElementsByTagName( 'img' );
	/** @var DOMElement $img */
	foreach ( $imgs as $img ) {
		$parent = $img->parentNode;
		if ( $parent->tagName == 'p' ) {
			$parent_class = $parent->getAttribute( 'class' );
			$parent->setAttribute( 'class', "$parent_class wp-caption" );
		}
	}

	$html = $document->saveHTML();

	return $html;
}

add_filter( 'the_content', 'wrap_content_images' );


function replace_em_dashes( $content ) {

	$content = str_replace( '&ndash;', '-', $content );

	return $content;
}


add_filter( 'the_content', 'replace_em_dashes' );