<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 5:37 PM
 */

$link = $template_args['link'];
?>

<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
	<?php echo $link['title']; ?>
</a>
