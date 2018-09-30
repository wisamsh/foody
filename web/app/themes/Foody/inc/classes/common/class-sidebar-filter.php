<?php

use Handlebars\Handlebars;

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 6:17 PM
 */
class SidebarFilter
{

    const FILTER_OPTIONS_ID = 'foody_search_options';
    const FILTER_SETTINGS_SELECTOR = 'groups';

    const FILTER_SECTIONS_SELECTOR = 'sections';

    private $engine;

    /**
     * SidebarFilter constructor.
     */
    public function __construct()
    {
        $this->engine = new Handlebars;
    }


    public function get_filter()
    {
        return $this->the_filter(false);
    }

    public function the_filter($echo = true)
    {

        $title = get_field('title', self::FILTER_OPTIONS_ID);
        $accordion_id = 'foody-filter';


        $main_accordion_args = array(
            'title' => $title,
            'id' => $accordion_id,
            'content' => $this->get_accordion_content(),
            'return' => !$echo,
            'title_classes' => 'main-title filter-title',
            'title_icon' => 'icon-filter'
        );

        return foody_get_template_part(
            get_template_directory() . '/template-parts/common/accordion.php',
            $main_accordion_args
        );
    }


    /**
     * The same as @see the_list() but
     * returns the content instead of displaying it.
     * @param array $list
     * @return bool|string
     */
    private function get_list($list)
    {
        return $this->the_list($list, false);
    }

    /**
     * @param array $list
     * @param bool $echo
     * @return bool|string
     */
    private function the_list($list, $echo = true)
    {

        $title = $list['title'];
        $type = $list['type'];


        $accordion_args = array(
            'title' => $title,
            'id' => $type . '-' . uniqid(),
            'content' => ''
        );

        $template = " <div class=\"md-checkbox\">
                    <input id=\"{{id}}\" type=\"checkbox\" name=\"{{id}}\"  data-exclude=\"{{exclude}}\" data-value=\"{{value}}\" data-type=\"{{type}}\">
                    <label for=\"{{id}}\">
                        {{label}}
                    </label>
                </div>";

        foreach ($list['checkboxes'] as $checkbox) {
            $item = $this->engine->render($template, array(
                'id' => $checkbox['value'],
                'exclude' => $checkbox['exclude'],
                'value' => $checkbox['value'],
                'type' => $checkbox['type'],
                'label' => $checkbox['title']
            ));

            $accordion_args['content'] .= $item;

        }

        $accordion_args['return'] = !$echo;

        return foody_get_template_part(
            get_template_directory() . '/template-parts/common/accordion.php',
            $accordion_args
        );
    }


    /**
     *  TODO document!
     * @return string
     */
    public function get_accordion_content()
    {
        $content = '';
        if (have_rows('filters_list', 'foody_search_options')) {
            $filters_list = get_field('filters_list', 'foody_search_options');


            $lists = array_map(function ($list) {
                $type = $list['type'];

                $list_title = $list['title'];

                $values = $list['values'];

                $exclude_all = $list['exclude_all'];


                $checkboxes = array_map(function ($value_arr) use ($type, $exclude_all) {

                    $exclude = $value_arr['exclude'] || $exclude_all;

                    $exclude = $exclude ? 'true' : 'false';

                    $checkbox_item = [
                        'type' => $type,
                        'value' => $value_arr['value'],
                        'exclude' => $exclude,
                        'title' => $value_arr['title']
                    ];

                    $switch_type = $value_arr['switch_type'];

                    if ($switch_type) {
                        $item_type = $value_arr['value_group'];
                        $checkbox_item['type'] = $item_type['type'];
                        $checkbox_item['value'] = $item_type['value'];
                    }

                    if (empty($checkbox_item['title'])) {
                        $checkbox_item['title'] = $this->get_item_title($checkbox_item['value'], $checkbox_item['type']);
                    }

                    return $checkbox_item;
                }, $values);

                return [
                    'title' => $list_title,
                    'checkboxes' => $checkboxes,
                    'type' => $type
                ];

            }, $filters_list);

            foreach ($lists as $list) {
                $content .= $this->get_list($list);
            }
        }

        return $content;
    }

    /**
     * @param $id
     * @param $type
     * @return int|null|string|WP_Error
     */
    private function get_item_title($id, $type)
    {

        $title = '';
        switch ($type) {
            case 'categories':
            case 'tags':
            case 'limitations':
                $title = get_term_field('name', $id, $this->type_to_taxonomy($type));
                break;

            case 'ingredients':
            case 'accessories':
            case 'techniques':
                $title = get_the_title($id);
                break;
        }

        return $title;
    }

    /**
     * @param $type
     * @return string
     */
    private function type_to_taxonomy($type)
    {
        $tax = '';
        switch ($type) {
            case 'categories':
                $tax = 'category';
                break;
            case 'tags':
                $tax = 'post_tag';
                break;
            case 'limitations':
                $tax = 'limitations';
                break;
        }

        return $tax;
    }

}