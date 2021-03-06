<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 12/24/18
 * Time: 3:58 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$title = $template_args['title'];
$items = $template_args['items'];

$max_mobile_items = 4;

?>

<section class="promotion-listing">

    <div class="title promotion-listing-block-title">
		<?php echo $title ?>
    </div>

    <ul class="promoted-items d-flex flex-row">

		<?php $i = 1; ?>
		<?php foreach ( $items as $item ): ?>

			<?php
			$item_class = 'promoted-item col';
			if ( $i > $max_mobile_items ) {
				$item_class = "$item_class d-none d-lg-block";
			}

			?>

            <li class="<?php echo $item_class ?>">
                <a href="<?php echo $item['link']['url']; ?>" target="<?php echo $item['link']['target']; ?>">
                    <div class="promotion-item-listing">
                        <div class="image-container">
                            <picture>
                                <source media="(min-width: 415px)" srcset="<?php echo $item['image']; ?>"
                                ">
                                <source media="(max-width: 414px)"
                                        srcset="<?php echo $item['mobile_image']; ?>"
                                ">
                                <img src="<?php echo $item['image'] ?>" alt="<?php echo $item['title']; ?>">
                            </picture>
                        </div>

                        <div class="promotion-listing-title"><?php echo $item['title']; ?></div>
                    </div>
                </a>

            </li>

			<?php $i ++; ?>
		<?php endforeach; ?>
    </ul>


</section>
