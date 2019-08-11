<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 5:37 PM
 */

$link = $template_args['link'];

//Link is empty - has no url
if ( ! ( ! empty( $link ) && isset( $link['url'] ) && ! empty( $link['url'] ) ) ) {
	$link = '';
}
$title = '';
if ( ! empty( $link ) && isset( $link['title'] ) && ! empty( $link['title'] ) ) {
	$title = $link['title'];
}
if ( empty( $title ) ) {
	$title = isset( $template_args['title'] ) ? $template_args['title'] : '';
}
?>
<?php if ( ! empty( $link ) ): ?>
    <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
		<?php echo $title; ?>
    </a>
<?php endif; ?>
