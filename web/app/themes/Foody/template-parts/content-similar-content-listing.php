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
<?php //WISAM : Tiktok video 
if(get_field("tiktok_video", get_the_ID())){
    echo get_field("tiktok_video", get_the_ID());
}

?>
<?php if (!empty($items)) { ?>
    <div class="title similar-content-listing-block-title">
        <?php echo $title; ?>
    </div>

    <?php if (!wp_is_mobile()) { ?>

        <ul class="similar-content-items d-flex flex-row">

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
                            <div class="image-container">
                                <picture>
                                    <source media="(min-width: 415px)" srcset="<?php echo $item['image']; ?>"
                                    ">
                                    <!--                                <source media="(max-width: 414px)"-->
                                    <!--                                        srcset="-->
                                    <?php //echo $item['mobile_image']; ?><!--"-->
                                    <!--                                ">-->
                                    <img src="<?php echo $item['image'] ?>" alt="<?php echo $item['title']; ?>">
                                </picture>
                            </div>

                            <div class="similar-content-listing-title"><?php echo $item['title']; ?></div>
                        </div>
                    </a>

                </li>

                <?php $i++; ?>
            <?php endforeach; ?>
        </ul>
    <?php } else { ?>
        <?php $items = array_chunk($items, 2); ?>
        <?php if (!empty($items)) { ?>
            <?php for ($index = 0; $index < 2; $index++) { ?>

                <?php if (isset($items[$index])) { ?>
                    <ul class="similar-content-items d-flex flex-row">
                    <?php $i = 1; ?>
                    <?php foreach ($items[$index] as $item): ?>

                        <?php
                        $item_class = 'similar-content-item col';
                        if ($i > $max_mobile_items) {
                            $item_class = "$item_class d-none d-lg-block";
                        }

                        ?>

                        <li class="<?php echo $item_class ?>">
                            <a href="<?php echo $item['link']; ?>" target="<?php echo $item['link']; ?>">
                                <div class="similar-content-item-listing">
                                    <div class="image-container">
                                        <picture>
                                            <source media="(min-width: 415px)" srcset="<?php echo $item['image']; ?>"
                                            ">
                                            <!--                                <source media="(max-width: 414px)"-->
                                            <!--                                        srcset="-->
                                            <?php //echo $item['mobile_image']; ?><!--"-->
                                            <!--                                ">-->
                                            <img src="<?php echo $item['image'] ?>" alt="<?php echo $item['title']; ?>">
                                        </picture>
                                    </div>

                                    <div class="similar-content-listing-title"><?php echo $item['title']; ?></div>
                                </div>
                            </a>

                        </li>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                    </ul>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    <?php }
} ?>

