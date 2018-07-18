<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

global $foody_page;

/** @var Foody_Recipe $recipe */
$recipe = $foody_page;
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

</section>

<section class="recipe-ingredients box">

    <div class="recipe-ingredients-top row justify-content-between">
        <h2 class="title col-6">
            מצרכים
        </h2>

        <div class="amount-container col-6">
            <label for="number-of-dishes">
                כמות מנות
            </label>
            <input name="amount" type="number" id="number-of-dishes"
                   value="<?php echo $recipe->getNumberOfDishes() ?>"
                   data-amount="<?php echo $recipe->getNumberOfDishes() ?>"
            >
        </div>

    </div>

    <div class="recipe-ingredients-container row">

        <?php $recipe->the_ingredients() ?>
    </div>

</section>

<section class="recipe-content">

    <div class="content-container">
        <?php echo $recipe->body ?>
    </div>

</section>

<section class="recipe-notes box">
    <?php $recipe->the_notes() ?>
</section>

<section class="recipe-rating box">
    <?php $recipe->the_rating() ?>
</section>

<section class="recipe-nutrition box">

    <h2 class="title">
        ערכים תזונתיים
    </h2>

    <div class="nutrition-container">
        <?php $recipe->the_nutrition() ?>
    </div>

</section>

<section class="recipe-accessories">
    <?php $recipe->the_accessories() ?>
</section>

<section class="recipe-techniques">
    <?php $recipe->the_techniques() ?>
</section>

<section class="recipe-tags">

    <h2 class="title">
        תגיות
    </h2>

    <?php $recipe->the_tags() ?>

</section>

<section class="recipe-how-i-did">
    <?php $recipe->how_i_did(); ?>

</section>

<section class="recipe-comments">
    <?php $recipe->comments(); ?>
</section>

