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
      "image": [
        "<?php echo $recipe->getImage() ?>",
        ],
      "author": {
        "@type": "Person",
        "name": "<?php echo $recipe->getAuthorName() ?>"
      },
      "datePublished": "<?php echo get_the_date('Y-m-d') ?>",
      "description": "<?php echo $recipe->getDescription() ?>",
      "prepTime": "PT20M",
      "cookTime": "PT30M",
      "totalTime": "PT50M",
      "keywords": "cake for a party, coffee",
      "recipeYield": "10 servings",
      "recipeCategory": "Dessert",
      "recipeCuisine": "American",
      "nutrition": {
        "@type": "NutritionInformation",
        "calories": "270 calories"
         },
      "recipeIngredient": [
        "2 cups of flour",
        "3/4 cup white sugar",
        "2 teaspoons baking powder",
        "1/2 teaspoon salt",
        "1/2 cup butter",
        "2 eggs",
        "3/4 cup milk"
       ],
      "recipeInstructions": [
          {
          "@type": "HowToStep",
          "text": "Preheat the oven to 350 degrees F. Grease and flour a 9x9 inch pan."
          },
          {
          "@type": "HowToStep",
          "text": "In a large bowl, combine flour, sugar, baking powder, and salt."
          },
          {
          "@type": "HowToStep",
          "text": "Mix in the butter, eggs, and milk."
          },
          {
          "@type": "HowToStep",
          "text": "Spread into the prepared pan."
          },
          {
          "@type": "HowToStep",
          "text": "Bake for 30 to 35 minutes, or until firm."
          },
          {
          "@type": "HowToStep",
          "text": "Allow to cool."
         }
      ],
      "review": {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "4",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "Julia Benson"
        },
        "datePublished": "2018-05-01",
        "reviewBody": "This cake is delicious!",
        "publisher": "The cake makery"
        },
      "aggregateRating": {
      "@type": "AggregateRating",
        "ratingValue": "5",
        "ratingCount": "18"
  },
  "video": [
     {
    "name": "How to make a Party Coffee Cake",
    "description": "This is how you make a Party Coffee Cake.",
    "thumbnailUrl": [
      "https://example.com/photos/1x1/photo.jpg",
      "https://example.com/photos/4x3/photo.jpg",
      "https://example.com/photos/16x9/photo.jpg"
     ],
    "contentUrl": "http://www.example.com/video123.flv",
    "embedUrl": "http://www.example.com/videoplayer.swf?video=123",
    "uploadDate": "2018-02-05T08:00:00+08:00",
    "duration": "PT1M33S",
    "interactionCount": "2347",
    "expires": "2019-02-05T08:00:00+08:00"
   }
  ]
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


<!--<section class="recipe-rating box">-->
<!--    --><?php //$recipe->the_rating() ?>
<!--</section>-->


<?php if ($recipe->has_nutrients()): ?>

    <section class="recipe-nutrition box">

        <?php $recipe->the_nutrition() ?>

    </section>

<?php endif; ?>


<section class="recipe-categories categories">
    <?php $recipe->the_categories() ?>
</section>

<section class="recipe-accessories">
    <?php $recipe->the_accessories() ?>
</section>

<section class="recipe-techniques">
    <?php $recipe->the_techniques() ?>
</section>

<?php if ($recipe->has_tags()): ?>

    <section class="recipe-tags tags">

        <h2 class="title">
            <?php echo __('תגיות', 'foody') ?>
        </h2>

        <?php $recipe->the_tags() ?>

    </section>

<?php endif; ?>

<section class="newsletter">
    <?php $recipe->newsletter(); ?>

</section>

<section class="recipe-how-i-did">
    <?php $recipe->how_i_did(); ?>

</section>

<section class="recipe-comments">
    <?php $recipe->comments(); ?>
</section>


<section class="recipe-sidebar-mobile d-block d-lg-none">
    <?php $recipe->the_mobile_sidebar_content(); ?>
</section>



