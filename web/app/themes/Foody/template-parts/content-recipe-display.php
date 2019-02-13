<?php
/**
 * Display a recipe's content.
 * Used in recipe and playlist pages
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/20/18
 * Time: 8:47 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Recipe $recipe */
$recipe = $template_args['recipe'];
?>


<script type="application/ld+json">
     {
      "@context": "http://schema.org/",
      "@type": "Recipe",
      "name": "<?php echo get_the_title() ?>",
      "image": "<?php echo $recipe->getImage() ?>",
      "author": {
        "@type": "Person",
        "name": "<?php echo $recipe->getAuthorName() ?>"
      },
      "datePublished": "<?php echo get_the_date('Y-m-d') ?>",
      "description": "<?php echo $recipe->getDescription() ?>",
      "cookTime": "PT30M",
      "totalTime": "PT50M",
      "keywords": "<?php echo implode(',', wp_get_post_tags()) ?>",
      "recipeYield": "<?php echo $recipe->number_of_dishes ?>",
      "recipeCategory": "<?php echo $recipe->get_primary_category_name() ?>",
      "nutrition": <?php echo $recipe->get_jsonld_nutrients() ?>,
      "recipeIngredient": <?php echo $recipe->get_ingredients_jsonld()?>
}

</script>

<section class="recipe-overview">

    <?php $recipe->the_overview() ?>

    <section class="preview">
        <?php $recipe->preview(); ?>
    </section>

</section>

<section class="recipe-ingredients box">

    <div class="recipe-ingredients-top row justify-content-between">
        <h2 class="title">
            <?php echo $recipe->the_ingredients_title() ?>
        </h2>
        <div class="amount-container">
            <?php $recipe->calculator(); ?>
        </div>
    </div>

    <div class="recipe-ingredients-container row">

        <?php $recipe->the_ingredients() ?>
    </div>

</section>

<section class="recipe-purchase-buttons d-block d-lg-none">
    <?php $recipe->the_purchase_buttons(); ?>
</section>

<section class="recipe-content">

    <div class="content-container">
        <?php echo $recipe->body ?>
    </div>

</section>


<?php $recipe->the_notes() ?>


<?php if ($recipe->has_nutrients() && false): ?>

    <section class="recipe-nutrition box no-print">

        <?php $recipe->the_nutrition() ?>

    </section>

<?php endif; ?>


<section class="recipe-categories categories no-print">
    <?php $recipe->the_categories() ?>
</section>

<section class="recipe-accessories">
    <?php $recipe->the_accessories() ?>
</section>

<section class="recipe-techniques">
    <?php $recipe->the_techniques() ?>
</section>

<?php if ($recipe->has_tags()): ?>

    <section class="recipe-tags tags no-print">

        <h2 class="title">
            <?php echo __('תגיות', 'foody') ?>
        </h2>

        <?php $recipe->the_tags() ?>

    </section>

<?php endif; ?>

<section class="newsletter no-print">
    <?php $recipe->newsletter(); ?>

</section>

<section class="recipe-how-i-did no-print">
    <?php $recipe->how_i_did(); ?>

</section>

<section class="recipe-comments no-print">
    <?php $recipe->comments(); ?>
</section>


<section class="recipe-sidebar-mobile d-block d-lg-none no-print">
    <?php $recipe->the_mobile_sidebar_content(); ?>
</section>



