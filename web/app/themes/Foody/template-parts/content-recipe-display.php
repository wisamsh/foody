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

//      "nutrition": <?php echo $recipe->get_jsonld_nutrients(),
// "video": <?php echo $recipe->get_jsonld_video()
?>


    <script async defer type="application/ld+json" id="recipe-schema">
     {
      "@context": "http://schema.org/",
      "@type": "Recipe",
<?php
        $author_name_for_schema = $recipe->escape_AuthorName_for_schema();
        $aggregateRating = $recipe->get_jsonld_aggregateRating(get_the_ID());
        if ($aggregateRating != false) { ?>
      "aggregateRating": <?php echo $aggregateRating; ?>,
<?php } ?>
      "name": "<?php echo addslashes(get_the_title()) ?>",
      "nutrition": <?php echo $recipe->get_jsonld_nutrients() ?>,
      "image": <?php echo $recipe->get_images_gallery_repeater() ?>,
      "author": {
        "@type": "Person",
        "name": "<?php echo $author_name_for_schema ?>"
      },
      "datePublished": "<?php echo get_the_date('Y-m-d') ?>",
      "description": "<?php echo str_replace('"', '', $recipe->getDescription()) ?>",
      "cookTime": "<?php echo $recipe->time_to_iso8601_duration('preparation_time') ?>",
      "totalTime": "<?php echo $recipe->time_to_iso8601_duration('total_time') ?>",
      "keywords": "<?php echo implode(',', $recipe->get_tags_names()) ?>",
      "recipeYield": "<?php echo $recipe->number_of_dishes ?>",
      "recipeCategory": "<?php echo addslashes($recipe->get_primary_category_name()) ?>",
      "recipeIngredient": <?php echo $recipe->get_ingredients_jsonld() ?>,
      "recipeInstructions": "<?php echo str_replace(['/', "\\"], ' ', str_replace('"', '״', str_replace(array("\r", "\n", "\t"), "", wp_strip_all_tags(get_the_content($recipe))))) ?>"
}
    </script>
<?php
$promotion_area_group = get_field('promotion_area', $recipe->id);
if (isset($promotion_area_group['text']) && !empty($promotion_area_group['text'])) { ?>
    <section class="promotion-area no-print">
        <?php $recipe->the_promotion_area($promotion_area_group); ?>
    </section>
<?php }
//Foody_Header::getPrintHeader(true);
?>

    <section class="recipe-overview no-print">

        <?php $recipe->the_overview() ?>

        <section class="preview">
            <?php $recipe->preview(); ?>
        </section>

    </section>
  <section class="recipe-overview-print print-desktop">
      <?php echo $recipe->the_print_overview() ?>
      <div class="image-and-rating-print">
          <?php echo $recipe->the_print_main_image() ?>
<!--          --><?php //echo $recipe->the_print_rating() ?>
      </div>
  </section>
<?php
$comments_rating_preps_group = get_field('comments_rating_component', $recipe->id);
 if ( isset($comments_rating_preps_group['number_of_preps']) ){
     $comments_rating_preps_group['number_of_preps'] = 7;
     ?>
     <section class="comments-rating-prep-container no-print">
         <?php $recipe->get_comments_rating_preps_component($comments_rating_preps_group['number_of_preps']) ?>
     </section>
 <?php } else { ?>
     <section class="comments-rating-prep-container no-print">
         <?php $recipe->get_comments_rating_preps_component($comments_rating_preps_group['number_of_preps']) ?>
     </section>
 <?php } ?>


<?php if ($recipe->substitute_all_button != null) { ?>
    <section class="substitute-all">
        <div class="substitute-all-btn" data-opposite="<?php echo $recipe->substitute_all_button['restore']; ?>"
             data-current="substitute" style="display: none">
            <?php echo $recipe->substitute_all_button['substitute']; ?>
        </div>
    </section>
<?php } ?>

    <section id="recipe-ingredients" class="recipe-ingredients box">

        <div class="recipe-ingredients-top row justify-content-between">
            <h2 class="title no-print">
                <?php echo $recipe->the_ingredients_title() ?>
            </h2>
            <div class="title-with-line print">
                <h2 class="title">
                    <?php echo $recipe->the_ingredients_title() ?>
                </h2>
                <hr class="title-line">
            </div>
            <div class="amount-container no-print">
                <?php $recipe->calculator(); ?>
            </div>
        </div>

        <div class="recipe-ingredients-container row">

            <?php $recipe->the_ingredients() ?>
        </div>

        <div class="ingredients-area-links no-print">
            <?php $recipe->the_conversion_table_link(); ?>
            <?php if(get_field('enable_vegan_btn', $recipe->get_id())){ ?>
                <div class="transform-to-vegetarian"><?php echo __('המרת המתכון לטבעוני') ?></div>
            <?php } ?>
        </div>
    </section>
    <section class="purchase-buttons">
        <?php $recipe->the_purchase_buttons(); ?>
    </section>
    <hr class="title-line">
    <section class="recipe-content <?php echo $recipe->is_content_by_steps() ? 'with-steps' : ''?>">
        <?php $recipe->get_relevant_content(); ?>
    </section>

<?php $recipe->the_notes() ?>
        <section class="recipe-sponsor-container box no-print">
        	<?php $recipe->the_sponsor() ?>
        </section>

<?php if ($recipe->show_google_adx()): ?>
    <section class="google-adx-container col-lg-9 col-12">
        <?php $recipe->the_google_adx(); ?>
    </section>
<?php endif; ?>

<?php
$enable_tip = get_field('enable_tip', $recipe->get_id());
if (!empty($enable_tip) && $enable_tip) { ?>
    <section class="system-tip-container no-print">
        <?php $recipe->get_system_tip(); ?>
    </section>
<?php } ?>

<?php
$similar_content = get_field('similar_content_group', $recipe->get_id());
// && $similar_content['active_similar_content'][0] == __('הצג')
//if (!empty($similar_content) && !empty($similar_content['active_similar_content']) ) { ?>
    <section class="recipe_similar_content no-print">
        <?php

            $recipe->get_similar_content($similar_content);
        ?>
    </section>
<?php //} ?>




    <section class="recipe-how-i-did no-print">
        <?php $recipe->how_i_did(); ?>

    </section>


    <section class="recipe-comments no-print">
        <?php $recipe->comments(); ?>
    </section>

<?php if(has_category('',$recipe->id)): ?>
    <section class="recipe-categories categories no-print">
    	<?php $recipe->the_categories() ?>
    </section>
<?php endif; ?>

    <section class="recipe-accessories accessories no-print">
    	<?php $recipe->the_accessories() ?>
    </section>

    <section class="recipe-techniques techniques no-print">
    	<?php $recipe->the_techniques() ?>
    </section>

<?php if ( $recipe->has_tags() ): ?>

        <section class="recipe-tags tags no-print">

            <h2 class="title">
    			<?php echo __( 'תגיות', 'foody' ) ?>
            </h2>

    		<?php $recipe->the_tags() ?>

        </section>

<?php endif; ?>

    <section class="newsletter no-print">
        <?php $recipe->newsletter(); ?>

    </section>


<section class="recipe-sidebar-mobile d-block d-lg-none no-print">
    <?php $recipe->the_mobile_sidebar_content(); ?>
</section>

<?php if ( function_exists( 'footabc_add_code_to_content' ) ): ?>
        <section class="footab-container no-print">
    		<?php echo footabc_add_code_to_content(); ?>
        </section>
<?php endif; ?>
<div class="print-footer print">
    <span class="footer-text"><?php echo __('לעוד מתכונים חפשו פודי בגוגל או היכנסו ל- foody.co.il') ?></span>
</div>
