<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 3:36 PM
 */

/** @noinspection PhpUndefinedVariableInspection */

global $post;
$foody_brands = $template_args['brands'];
$foody_brands_title = $template_args['title'];

?>
<h2 class="brands-title"><?php echo $foody_brands_title; ?></h2>
<ul class="brands-list">
    <?php foreach ($foody_brands as $brand) {
        $link = !empty($brand["link"]) && is_array($brand["link"]) && isset($brand["link"]['url']) && !empty($brand["link"]['url']) ? $brand["link"] : false;

        if ($link) { ?>
            <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
        <?php } ?>
        <li class="brand-item-container">
            <img src="<?php echo $brand['logo']['url']; ?>">
        </li>
        <?php if ($link) { ?>
            </a>
        <?php } ?>
    <?php } ?>
</ul>

