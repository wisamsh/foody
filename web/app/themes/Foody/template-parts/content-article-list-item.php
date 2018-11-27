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

?>

<div class="article-item feed-item">
    <a href="<?php echo $article->link ?>">
        <div class="image-container main-image-container">
            <img class="article-item-image feed-item-image" src="<?php echo $article->getImage() ?>" alt="">
        </div>
    </a>


    <section class="article-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <a href="<?php echo $article->get_author_link() ?>">
                <img src="<?php echo $article->getAuthorImage() ?>" alt="">
            </a>
        </div>
        <section class="col-11">
            <h3>
                <a href="<?php echo $article->link ?>">
                    <?php echo $article->getTitle() ?>
                </a>
            </h3>
            <ul>
                <li>
                    <a href="<?php echo $article->get_author_link() ?>">
                        <?php echo $article->getAuthorName() ?>
                    </a>
                </li>
                <li>
                    <?php echo $article->getViewCount() ?>
                </li>
                <li>
                    <?php echo $article->getPostedOn() ?>
                </li>
            </ul>
            <div class="description">
                <?php echo $article->getDescription() ?>
            </div>
        </section>


    </section>
</div>
