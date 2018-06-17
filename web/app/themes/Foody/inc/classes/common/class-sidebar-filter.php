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

    private const FILTER_OPTIONS_ID = 'foody_search_options';
    private const FILTER_SETTINGS_SELECTOR = 'groups';

    private const FILTER_SECTIONS_SELECTOR = 'sections';

    private $settings;

    private $engine;

    /**
     * SidebarFilter constructor.
     */
    public function __construct()
    {
        $this->settings = $this->load_settings();
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
            'content' => '',
            'return' => !$echo
        );

        // Groups loop start
        if (have_rows(self::FILTER_SETTINGS_SELECTOR, self::FILTER_OPTIONS_ID)) {

            while (have_rows(self::FILTER_SETTINGS_SELECTOR, self::FILTER_OPTIONS_ID)) : the_row();

                // here the_row is the main group, e.g Kitchen

                $type = get_sub_field('type');

                // Sections loop start
                if (have_rows(self::FILTER_SECTIONS_SELECTOR)) {

                    while (have_rows(self::FILTER_SECTIONS_SELECTOR)) : the_row();

                        $title = get_sub_field('title');

                        $exclude_all = get_sub_field('exclude_all');

                        $list = $this->the_list($title, $type, $exclude_all, $echo);
                        if ($list) {
                            $main_accordion_args['content'] .= $list;
                        }

                    endwhile;
                }
                // Sections loop end

            endwhile;
        }
        // Groups loop end

        return foody_get_template_part(
            get_template_directory() . '/template-parts/common/accordion.php',
            $main_accordion_args
        );
    }


    /**
     * The same as @see the_list() but
     * returns the content instead of displaying it.
     * @param $title
     * @param $type
     * @param $exclude_all
     * @return bool|string
     */
    private function get_list($title, $type, $exclude_all)
    {
        return $this->the_list($title, $type, $exclude_all, true);
    }

    /**
     * @param $title
     * @param $type
     * @param $exclude_all
     * @param bool $echo
     * @return bool|string
     */
    private function the_list($title, $type, $exclude_all, $echo = true)
    {

        $accordion_args = array(
            'title' => $title,
            'id' => $type . '-' . uniqid(),
            'content' => ''
        );

        $template = '<form><div class="checkbox" data-exclude="{{exclude}}" data-value="{{value}}" data-type="{{type}}">
                        <label for="{{id}}">
                            <input type="checkbox" id="{{id}}">
                          <span class="checkbox-label-text">
                          
                          {{label}}
                          </span>
                                       
                        </label>
                    </div></form>';


        $items_counter = 1;
        if (have_rows('list')) {
            while (have_rows('list')): the_row();

                $value = get_sub_field('value');

                $label = get_sub_field('title');

                $exclude = get_sub_field('exclude_from_search');

                $exclude = $exclude || $exclude_all;

                $checkbox_id = 'checkbox-' . uniqid();

                $item = $this->engine->render($template, array(
                    'id' => $checkbox_id,
                    'exclude' => $exclude,
                    'value' => $value,
                    'type' => $type,
                    'label' => $label,
                ));

                $accordion_args['content'] .= $item;

                $items_counter++;
            endwhile;
        }


        $accordion_args['return'] = !$echo;

        return foody_get_template_part(
            get_template_directory() . '/template-parts/common/accordion.php',
            $accordion_args
        );

    }


    private function load_settings()
    {
        $settings = get_field(self::FILTER_SETTINGS_SELECTOR, self::FILTER_OPTIONS_ID);
        return $settings;
    }

}