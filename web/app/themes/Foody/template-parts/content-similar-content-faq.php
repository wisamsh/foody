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

$max_mobile_items = 6
;

?>

<?php if (!empty($items)) { ?>
    <div class="title similar-content-listing-block-title">
        <?php echo $title; ?>
    </div>



        <ul class="similar-content-items flex-row">

            <?php $i = 1; ?>
            <?php foreach ($items as $item): ?>

                <?php
                $item_class = 'similar-content-item col';
                if ($i > $max_mobile_items) {
                    $item_class = "$item_class d-none d-lg-block";
                }

                ?>

                <li class="<?php echo $item_class ?>">
                    <a href="<?php echo $item['link']; ?>" target="<?php echo $item['link']; ?>">
                        <div class="similar-content-item-listing">
                            <div class="similar-content-listing-title"><?php echo $item['title']; ?></div>
                        </div>
                    </a>

                </li>

                <?php $i++; ?>
            <?php endforeach; ?>
        </ul>

<?php } ?>

