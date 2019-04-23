<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/13/18
 * Time: 11:43 AM
 */

$product = $template_args['product'];

?>
<section class="product-container">

    <div class="foody-product-item">
        <img src="<?php echo $product['image']['url'] ?>" alt="<?php echo $product['image']['alt'] ?>">
        <div class="product-content">

            <h2 class="product-title"><?php echo $product['title'] ?></h2>
            <div class="product-subtitle"><?php echo $product['subtitle'] ?></div>
        </div>
        <a class="product-link" href="<?php echo $product['link']['url'] ?>"
           target="<?php echo $product['link']['target'] ?>">
			<?php echo $product['link_text'] ?>
        </a>
    </div>

</section>

