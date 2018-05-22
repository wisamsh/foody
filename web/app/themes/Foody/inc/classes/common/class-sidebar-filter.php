<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 6:17 PM
 */
class SidebarFilter
{


    /**
     * SidebarFilter constructor.
     */
    public function __construct()
    {
    }


    public function tags_groups()
    {

        $template = '<div class="checkbox">
                        <input type="checkbox" id="checkbox1">
                        <label for="checkbox1">
                        placeholder
                        </label>
                    </div>';


        $template = '
                <div class="form-check">
                    <input class="form-check-input filled-in" type="checkbox" value="" id="placeholder">
                    <label class="form-check-label" for="placeholder">
                        placeholder
                    </label>
                </div>';

        $tags = array(
            'kitchen' => array(
                'name' => 'מטבח',
                'tags' => [
                    'סיני',
                    'מרוקאי',
                    'תאילנדי',
                    'וייטנאמי'
                ]
            ),
            'diet' => array(
                'name' => 'דיאטה',
                'tags' => [
                    'טבעוני',
                    'פליאו',
                    'ללא סוכר',
                    'ללא לקטוז',
                    'ללא גלוטן',
                    'ללא שומן'
                ]
            )
        );


        foreach ($tags as $key =>  $tag) {
            $title = $tag['name'];
            $content = "";
            $id = $key;
            foreach ($tag['tags'] as $item) {
                $template_copy = ''. $template;
                $content .= str_replace('placeholder',$item,$template_copy);
            }

            include(get_template_directory() . '/template-parts/common/accordion.php');

        }
    }

    private function accordion_tab()
    {

    }


}