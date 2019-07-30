<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/11/18
 * Time: 4:06 PM
 */

$context = $template_args['context'];
?>

<a class="load-more" id="load-more" tabindex="1" data-context="<?php echo $context ?>">
	<?php
	echo __( 'הצג עוד', 'Foody' );
	?>
    <i class="icon-show-more-arrow"></i>
</a>
