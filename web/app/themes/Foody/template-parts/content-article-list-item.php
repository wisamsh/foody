<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/27/18
 * Time: 5:15 PM
 */
/** @var Foody_Article $article */
/** @noinspection PhpUndefinedVariableInspection */
$article = $template_args['post'];
$args = foody_get_array_default($template_args, 'args', []);
$title_el = foody_get_array_default($args, 'title_el', 'h2');
$image_size = isset($args['image_size']) ? $args['image_size'] : 'list-item';

$target = '';
if (!empty($article->link_attrs['target'])) {
    $target = "target='{$article->link_attrs['target']}'";
}

$lazy = !empty($template_args['lazy']);

?>

<div class="article-item feed-item">
    <a href="<?php echo $article->link ?>" <?php echo $target ?>>
        <div class="image-container main-image-container">
            <?php if ($lazy): ?>
                <img src="<?php echo $GLOBALS['images_dir'] . 'recipe-placeholder.svg'?>" class="article-item-image feed-item-image lazyload" data-foody-src="<?php echo $article->getImage() ?>" alt="<?php echo image_alt_by_url($article->getImage())?>">
            <?php else: ?>
                <img class="article-item-image feed-item-image" src="<?php echo $article->getImage() ?>" alt="<?php echo image_alt_by_url($article->getImage())?>">

            <?php endif; ?>
            <?php if (!empty($label = $article->get_label())): ?>

                <div class="recipe-label">
                    <span>

                    <?php echo $label ?>
                    </span>
                </div>

            <?php endif; ?>
            <?php if ($article->video != null): ?>
                <div class="duration">
                    <i class="icon icon-timeplay">

                    </i>
                    <span>
                        <?php echo $article->getDuration() ?>
                    </span>


                </div>
            <?php endif; ?>
        </div>
    </a>

    <section class="feed-item-details-container">

        <section class="title-container">
            <<?php echo $title_el ?> class="grid-item-title">
            <a href="<?php echo $article->link ?>" <?php echo $target ?>>
                <?php echo $article->getTitle() ?>
            </a>
        </<?php echo $title_el ?>>

        <div class="description">
            <?php echo $article->getDescription() ?>
        </div>
    </section>
    <section class="article-item-details  d-flex">
        <div class="image-container col-12 nopadding">
            <a href="<?php echo $article->get_author_link() ?>">
                <img class="lazyload" src="<?php echo $GLOBALS['images_dir'] . 'author-placeholder.svg'?>" data-foody-src="<?php echo $article->getAuthorImage() ?>" alt="<?php echo $article->getAuthorName() ?>">
            </a>
            <ul>
                <li>
                    <a href="<?php echo $article->get_author_link() ?>">
                        <?php echo $article->getAuthorName() ?>
                    </a>
                </li>
                <li>
                    <?php echo $article->getViewCount() ?>
                </li>
                <!--                    <li>-->
                <!--                        --><?php //echo $article->getPostedOn() ?>
                <!--                    </li>-->
            </ul>
        </div>

    </section>
    </section>
</div>
