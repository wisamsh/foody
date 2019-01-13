<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Recipe $recipe */
/** @noinspection PhpUndefinedVariableInspection */
$recipe = $template_args['post'];
$args = foody_get_array_default($template_args, 'args', []);

$image_size = isset($args['image_size']) ? $args['image_size'] : 'list-item';
?>


<div class="recipe-item feed-item">
    <a href="<?php echo $recipe->link ?>">
        <div class="image-container main-image-container">
            <img class="recipe-item-image feed-item-image" src="<?php echo $recipe->getImage($image_size) ?>" alt="">

            <?php if (!empty($label = $recipe->get_label())): ?>

                <div class="recipe-label">
                         <span>

                    <?php echo $label ?>
                    </span>


                    <!--                    <svg xmlns="http://www.w3.org/2000/svg" width="50%" height="auto" viewBox="0 0 224 92">-->
                    <!--                        <defs>-->
                    <!--                            <filter id="a" width="104.6%" height="106.3%" x="-2%" y="-2.8%" filterUnits="objectBoundingBox">-->
                    <!--                                <feOffset dx="2" dy="2" in="SourceAlpha" result="shadowOffsetOuter1"/>-->
                    <!--                                <feGaussianBlur in="shadowOffsetOuter1" result="shadowBlurOuter1" stdDeviation="5"/>-->
                    <!--                                <feColorMatrix in="shadowBlurOuter1" result="shadowMatrixOuter1" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/>-->
                    <!--                                <feMerge>-->
                    <!--                                    <feMergeNode in="shadowMatrixOuter1"/>-->
                    <!--                                    <feMergeNode in="SourceGraphic"/>-->
                    <!--                                </feMerge>-->
                    <!--                            </filter>-->
                    <!--                            <linearGradient id="b" x1="50%" x2="50%" y1="0%" y2="100%">-->
                    <!--                                <stop offset="0%" stop-color="#A83C2F"/>-->
                    <!--                                <stop offset="100%" stop-color="#A92733"/>-->
                    <!--                            </linearGradient>-->
                    <!--                            <linearGradient id="c" x1="50%" x2="50%" y1="0%" y2="100%">-->
                    <!--                                <stop offset="0%" stop-color="#F35644"/>-->
                    <!--                                <stop offset="100%" stop-color="#EC3849"/>-->
                    <!--                            </linearGradient>-->
                    <!--                        </defs>-->
                    <!--                        <g fill="none" fill-rule="evenodd" filter="url(#a)" transform="translate(8 -8)">-->
                    <!--                            <path fill="url(#b)" d="M0 64.925l5.52 9.454V59.31z" transform="translate(0 16.093)"/>-->
                    <!--                            <path fill="url(#c)" d="M2.962.901C85.13-.132 152.375-.119 204.701.94l-12.467 29.903a3 3 0 0 0-.024 2.25l12.49 31.833c-61.007-1.332-129.24-1.332-204.7 0V3.901a3 3 0 0 1 2.962-3z" transform="translate(0 16.093)"/>-->
                    <!--                        </g>-->
                    <!--                        <text id="text1"  x="50%" y="50%" dominant-baseline="middle" text-anchor="middle">-->
                    <!--                            המלצת היום  המלצת היום  המלצת היום המלצת היום-->
                    <!--                        </text>-->
                    <!--                    </svg>-->

                </div>

            <?php endif; ?>
            <?php if ($recipe->video != null): ?>
                <div class="duration">
                    <i class="icon icon-timeplay">

                    </i>
                    <span>
                        <?php echo $recipe->getDuration() ?>
                    </span>


                </div>
            <?php endif; ?>
        </div>
    </a>
    <section class="recipe-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <a href="<?php echo $recipe->get_author_link() ?>">
                <img src="<?php echo $recipe->getAuthorImage() ?>" alt="">
            </a>
        </div>

        <section class="col-11">
            <h3>
                <a href="<?php echo $recipe->link ?>">
                    <?php echo $recipe->getTitle() ?>
                </a>
            </h3>
            <ul>
                <li>
                    <a href="<?php echo $recipe->get_author_link() ?>">
                        <?php echo $recipe->getAuthorName() ?>
                    </a>
                </li>
                <li>
                    <?php echo $recipe->getViewCount() ?>
                </li>
                <li>
                    <?php echo $recipe->getPostedOn() ?>
                </li>
            </ul>
            <div class="description">
                <?php echo $recipe->getDescription() ?>
            </div>


            <?php
            foody_get_template_part(
                get_template_directory() . '/template-parts/common/favorite.php',
                array(
                    'id' => $recipe->id,
                    'post' => $recipe
                )
            );
            ?>
        </section>


    </section>
</div>


