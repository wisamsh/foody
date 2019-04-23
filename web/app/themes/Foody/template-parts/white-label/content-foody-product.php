<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/13/18
 * Time: 11:43 AM
 */

$product = $template_args['product'];
$widget  = isset( $template_args['widget'] ) && $template_args['widget'] ? $template_args['widget'] : '';

?>

<?php if ( $widget ): ?>
    <section class="widget-product-container">

        <div class="foody-product-item">
            <h2 class="product-title"><?php echo $product['title'] ?></h2>
            <img src="<?php echo $product['image']['url'] ?>" alt="<?php echo $product['image']['alt'] ?>">
            <a class="product-link" href="<?php echo $product['link']['url'] ?>"
               target="<?php echo $product['link']['target'] ?>">
				<?php echo $product['link']['title'] ?>
            </a>
        </div>

    </section>
<?php else: ?>
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
<?php endif; ?>
