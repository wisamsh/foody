<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/29/18
 * Time: 4:46 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
//error_reporting(E_ERROR | E_PARSE);
error_reporting(0);


$ingredients_groups = $template_args['groups'];



//Wisam===========================================================================================
$Foody_Substitutes_Ingredients = new Foody_Substitutes_Ingredients();
echo $Foody_Substitutes_Ingredients->SwapStyle();

//JS Scripts : 
echo $Foody_Substitutes_Ingredients->swap_Script();
//Wisam==========================================================================================

$substitute_ingredients_filters = isset($template_args['substitute_ingredients_filters']) ? $template_args['substitute_ingredients_filters'] : null;
$counter = 0;
$recipe_id = isset($template_args['recipe_id']) ? $template_args['recipe_id'] : false;
//$rr = get_field("ingredients", get_the_id());
//print_r($rr);
?>



<?php foreach ($ingredients_groups as $ingredients_group){ ?>

    <div class="col-12 ingredients-group p-0">
        <h2 class="ingredients-group-title">
            <?php echo $ingredients_group['title']; 
			
			?>
        </h2>
        <ul class="ingredients-group-list">

            <?php /** @var Foody_Ingredient $ingredient */
			
            foreach ($ingredients_group['ingredients'] as $ingredient){ ?>
                <?php
                $id = "ingredient-" . $counter;
                $counter++;
				$TheSubIngPostID = '';
				//Wisam Change Getting Substitute ingredients =======================================================================
				if($ingredient->alternative_ing){
					 $TheSubIngPostID =  $ingredient->alternative_ing;
					 
				}
				$data_ing = $TheSubIngPostID ? "data-ingid=".$TheSubIngPostID : "";
				$classIngSub = $data_ing ? "ing_wrapp" : "";
                ?>
                <li class="ingredients<?php echo " " . $classIngSub;?>" id="<?php echo $id; ?>" <?php echo $data_ing;?> 
				<?php if($classIngSub){echo " style='overflow: initial !important;
					max-height: 500px !important;'";}?>>
                    <div class="ingredient Orginal_swapper_<?php echo $TheSubIngPostID;?>" id="ing_<?php echo $TheSubIngPostID;?>">
					
					
					 <?php $ingredient->the_amounts(true, $recipe_id) ;
					
					 
					 ?>
						
                    </div>
					<?php 
						 echo $Foody_Substitutes_Ingredients->get_text_image_Swap($TheSubIngPostID, get_the_ID());
						 echo $Foody_Substitutes_Ingredients->get_Swap_Type( $TheSubIngPostID, get_the_ID() );
						?>
										
                    <?php $ingredient->the_sponsored_ingredient(true, $recipe_id); ?>
                    <?php echo $ingredient->get_substitute_ingredient($substitute_ingredients_filters, $recipe_id); ?>
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


