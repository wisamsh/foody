<script>
    //FObject with data for Questions questions_events.js=======================================================
    //==========================================================================================================
    let FAQ_Details = new Object();
    FAQ_Details.title = '<?php echo get_the_title(); ?>';
    FAQ_Details.mainCategories = '<?php $maincat = $Foody_Questions->getQuestionAllCats();
                                    echo $maincat[0]['name']; ?>';
    FAQ_Details.accessories = '<?php
                                if (!empty($Foody_Questions->the_accessories_RAW())) {
                                    $acc = $Foody_Questions->the_accessories_RAW();
                                    echo (implode(" , ", $acc));
                                } else {

                                    echo ('');
                                }

                                ?> ';

    //the_Technics_RAW
    FAQ_Details.technics = '<?php
                            if(!empty($Foody_Questions->the_Technics_RAW())){
                            $Tech = $Foody_Questions->the_Technics_RAW();
                            echo (implode(" , ", $Tech));
                            }
                            else
                            {echo ('');}
                            ?>';


    FAQ_Details.tags = '<?php
                        if(!empty($Foody_Questions->the_Tags_RAW())){
                        $tags = $Foody_Questions->the_Tags_RAW();
                        echo (implode(' , ', $tags));
                        }
                        else{
                            echo ('');
                        }
                        ?>';

    FAQ_Details.subcategories = '<?php
                                    if(($Foody_Questions->the_categories_RAW())){
                                    $Sub_Categories = $Foody_Questions->the_categories_RAW();
                                    echo (implode(" , ", $Sub_Categories));
                                    }
                                    else{
                                        echo('');
                                    }
                                    ?>';
</script>