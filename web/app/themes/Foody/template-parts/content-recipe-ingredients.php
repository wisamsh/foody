<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/29/18
 * Time: 4:46 PM
 */

/** @noinspection PhpUndefinedVariableInspection */

error_reporting(0);


$ingredients_groups = $template_args['groups'];



//Wisam===========================================================================================
$Foody_Substitutes_Ingredients = new Foody_Substitutes_Ingredients(null);

$Get_Common_Rule_Array = $Foody_Substitutes_Ingredients->Get_Common_Rule_For_Merchandise();

echo $Foody_Substitutes_Ingredients->SwapStyle();

//JS Scripts :
echo $Foody_Substitutes_Ingredients->swap_Script();
//Wisam==========================================================================================

$substitute_ingredients_filters = isset($template_args['substitute_ingredients_filters']) ? $template_args['substitute_ingredients_filters'] : null;
$counter = 0;
$recipe_id = isset($template_args['recipe_id']) ? $template_args['recipe_id'] : false;
?>



<?php foreach ($ingredients_groups as $ingredients_group) 
{ ?>

    <div class="col-12 ingredients-group p-0">
        <h2 class="ingredients-group-title">
            <?php echo $ingredients_group['title'];

            ?>
        </h2>
        <ul class="ingredients-group-list">

            <?php /** @var Foody_Ingredient $ingredient */

            //print_r($ingredients_group['ingredients']);
            foreach ($ingredients_group['ingredients'] as $ingredient) { ?>
                <?php
                $id = "ingredient-" . $counter;
                $counter++;
                $TheSubIngPostID = '';
                //Wisam Change Getting Substitute ingredients =======================================================================
                if ($ingredient->alternative_ing) {
                    $TheSubIngPostID = $ingredient->alternative_ing;
                }
                $ing_orginal_ID = $ingredient->id;
                $data_ing = $TheSubIngPostID ? "data-ingid=" . $TheSubIngPostID : "";
                $classIngSub = $data_ing ? "ing_wrapp" : "";
                ?>
                <li class="ingredients<?php echo " " . $classIngSub; ?>" id="<?php echo $id; ?>" <?php echo $data_ing; ?> <?php if ($classIngSub) {
                                                                                                                                echo " style='overflow: initial !important;max-height: 500px !important;'";
                                                                                                                            }
                                                                                                                            ?> data-orginal="<?php echo $ing_orginal_ID; ?>">




                    <div class="ingredient Orginal_swapper_<?php echo $TheSubIngPostID; ?>" id="ing_<?php echo $TheSubIngPostID; ?>">


                        <?php $ingredient->the_amounts(true, $recipe_id);


                        ?>

                        <?php //=========================Wisam Say : Getting @Common_Rule_Array ======================================================================

                        //$ComRuleIngKey = array_search($ing_orginal_ID, $Get_Common_Rule_Array["ing"]);
                        foreach ($Get_Common_Rule_Array["ing"] as $ComRuleIngKey => $ComRuleIngVal) {
                            if ($ComRuleIngVal == $ing_orginal_ID) {
                                $show_sponsor_text = $Get_Common_Rule_Array["show_sponsor_text"][$ComRuleIngKey];
                                $show_sponsor_logo = $Get_Common_Rule_Array["show_sponsor_logo"][$ComRuleIngKey];
                                $logo_url = $Get_Common_Rule_Array["logo_url"][$ComRuleIngKey];
                                $sponser_Text = $Get_Common_Rule_Array["sponser_Text"][$ComRuleIngKey];
                                $ing_link = $Get_Common_Rule_Array["ing_link"][$ComRuleIngKey];
                                //echo $ComRuleIngKey;

                                if (trim($show_sponsor_text) == 1) {
                                    if ($ing_link) {
                                        $ingr_URL = '<a target="_blank" href="' . $ing_link . '">' . $sponser_Text . '';
                                    } else {
                                        $ingr_URL = $sponser_Text;
                                    }
                                    echo "<span class='sponser_Text'>" . $ingr_URL . "</span>";
                                }

                                if (trim($show_sponsor_logo == 1) && $logo_url != "") {

                                    if ($ing_link) {
                                        $Photo_comm_Url = '<a target="_blank" href="' . $ing_link . '"><img src="' . $logo_url . '"/></a>';
                                    } else {
                                        $Photo_comm_Url = '<img src="' . $logo_url . '"/>';
                                    }
                                    echo "<span class='commorcial_logo milr'>" . $Photo_comm_Url . "</span>";
                                }
                            }
                        }

                        ?>
                    </div>
                    <?php
                    echo $Foody_Substitutes_Ingredients->get_text_image_Swap($TheSubIngPostID, get_the_ID());
                    echo $Foody_Substitutes_Ingredients->get_Swap_Type($TheSubIngPostID, get_the_ID());
                    ?>

                    <?php //$ingredient->the_sponsored_ingredient(true, $recipe_id); 
                    ?>
                    <?php //echo $ingredient->get_substitute_ingredient($substitute_ingredients_filters, $recipe_id); 
                    ?>
                    <?php
                    // Add ingredient comment
                    if (!empty($ingredient->comment)) {
                        echo '<div class="comment">' . $ingredient->comment . '</div>';
                    }
                    ?>

                </li>

            <?php } ?>
        </ul>

    </div>


<?php } ?>