<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 10:51 AM
 */


function foody_widgets()
{


    $widgets = array(
        'foody_Categories_List_Widget',
        'foody_Search_Filter',
        'Foody_CategoriesAccordionWidget',
        'foody_Product_Widget'
    );

    foreach ($widgets as $widget) {
        register_widget($widget);
    }
}

add_action('widgets_init', 'foody_widgets');


add_filter('dynamic_sidebar_params', 'foody_wrap_widget_titles', 20);
function foody_wrap_widget_titles(array $params)
{

    // $params will ordinarily be an array of 2 elements, we're only interested in the first element
    $widget =& $params[0];
    $widget['before_title'] = '<div class="widgettitle">';
    $widget['after_title'] = '</div>';

    return $params;

}

function foody_dynamic_sidebar_ajax_loading($sidebar_id, $container_selector)
{
    ob_start();

    dynamic_sidebar($sidebar_id);

    $sidebar = ob_get_contents();

    ob_end_clean();
    $sidebar = escape_javascript_text($sidebar);
//    $sidebar = addslashes(preg_replace('/\s+/m', ' ', $sidebar));
    ?>
    <script async defer id="sidebar-loader-<?php echo $sidebar_id ?>">
        var sidebar = '<?php echo $sidebar; ?>';
        jQuery('<?php echo $container_selector?>').append(sidebar);
    </script>
    <?php

}

function escape_javascript_text($string)
{
    return str_replace(
        "\n",
        '\n',
        str_replace(
            '"',
            '\"',
            addcslashes(
                str_replace(
                    "\r",
                    '',
                    (string)$string),
                "\0..\37'\\"
            )
        )
    );
}