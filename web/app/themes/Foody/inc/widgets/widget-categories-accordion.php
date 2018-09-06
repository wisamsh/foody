<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/13/18
 * Time: 11:15 AM
 */
class Foody_CategoriesAccordionWidget extends Foody_Widget
{
    public static $foody_widget_id = 'categories_accordion_widget';

    const CSS_CLASSES = 'categories-accordion-widget';

    /**
     * To create the example widget all four methods will be
     * nested inside this single instance of the WP_Widget class.
     **/

    public function __construct()
    {
        $widget_options = array(
            'classname' => self::CSS_CLASSES,
            'description' => 'This is an Example Widget',
        );
        parent::__construct(self::$foody_widget_id, 'Categories Accordion', $widget_options);
    }

    public function form($instance)
    {

        $title = !empty($instance['title']) ? $instance['title'] : ''; ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>"/>
        </p><?php
    }

    protected function display($args, $instance)
    {


        $title = apply_filters('widget_title', $instance['title']);
        $title = '<h3 class="title"> <a href="' . get_permalink(get_page_by_path('קטגוריות')) . '">' . $title . '</a></h3>';
        echo $title;


        $this->build_categories_tree();
//
//        $categories = get_field('categories', 'widget_' . $this->id);
//
//        $num_of_categories = wp_is_mobile() ? 4 : 5;
//        $args = array(
//            'hide_empty' => 0
//        );
//        $categories_count = sizeof(get_categories($args)) - $num_of_categories;
//        echo '<div class="categories-listing d-flex flex-row" data-count="' . $categories_count . '">';
//
//        $count = 0;
//        foreach ($categories as $category) {
//            if ($count == $num_of_categories) {
//                break;
//            }
//            foody_get_template_part(get_template_directory() . '/template-parts/content-category-listing.php', array(
//                'name' => $category->name,
//                'image' => get_field('image', $category->taxonomy . '_' . $category->term_id)
//            ));
//
//            $count++;
//        }
//        echo '</div>';
    }


    private function build_categories_tree()
    {
        $categories = get_field('categories', 'widget_' . $this->id);

        $exclude = get_categories([
            'exclude' => array_map(function ($category) {
                return $category->term_id;
            }, $categories)
        ]);

        $exclude = array_map(function ($category) {
            return $category->term_id;
        }, $exclude);

        $ul = wp_list_categories([
            'exclude' => $exclude,
            'echo' => false
        ]);

        $ul = '<?xml encoding="UTF-8">' . '<ul>' . $ul . '</ul>';

        $arr = $this->ul_to_array($ul);

        $dom = new DOMDocument();

        $dom->loadHTML($ul);

        $listAsDom = $dom->getElementsByTagName('ul')->item(0);

        $arr = $this->domNodeToArray($listAsDom);

        $list = $arr['li']['ul']['li'];

//        $this->categories_array_to_accordion($list);

        echo $ul;

//        foreach ($categories as $category) {
//            $wp_category = get_category($category);
//
//            if ($wp_category instanceof WP_Term) {
//
//                $parent = $wp_category->parent;
//
//                if ($parent) {
//                    $categories_tree[$parent][] = $wp_category;
//                } else {
//
//                }
//            }
//
//        }
    }

    function domNodeToArray(DOMNode $node)
    {
        $ret = '';

        if ($node->hasChildNodes()) {
            if ($node->firstChild === $node->lastChild
                && $node->firstChild->nodeType === XML_TEXT_NODE
            ) {
                // Node contains nothing but a text node, return its value
                $ret = [
                    'type' => trim($node->nodeValue),
                    'href' => $node->attributes->getNamedItem('href')->nodeValue
                ];
            } else {
                // Otherwise, do recursion
                $ret = array();
                foreach ($node->childNodes as $child) {
                    if ($child->nodeType !== XML_TEXT_NODE) {
                        // If there's more than one node with this node name on the
                        // current level, create an array
                        if (isset($ret[$child->nodeName])) {
                            if (!is_array($ret[$child->nodeName])
                                || !isset($ret[$child->nodeName][0])
                            ) {
                                $tmp = $ret[$child->nodeName];
                                $ret[$child->nodeName] = array();
                                $ret[$child->nodeName][] = $tmp;
                            }

                            $ret[$child->nodeName][] = $this->domNodeToArray($child);
                        } else {
                            $ret[$child->nodeName] = $this->domNodeToArray($child);
                        }
                    }
                }
            }
        }

        return $ret;
    }


    private function categories_array_to_accordion($categories)
    {

        $accordion_args = [];

        $content = '';

        foreach ($categories as $category) {

            if (isset($category['ul'])) {

            } else {
                $content .= "<a href='{$category['a']['href']}'>{$category['a']['type']}</a>";
            }
        }


    }

    private function ul_to_accordion($ul, $title)
    {
        $accordion_args = [
            'title' => $title,
            'return' => 'true',
            'id' => uniqid()
        ];

        $content = '';
        foreach ($ul['li'] as $li) {
            $content .= "<a href='{$li['a']['href']}'>{$li['a']['type']}</a>";
        }

        $accordion_args['content'] = $content;

        return foody_get_template_part(get_template_directory() . '/template-parts/common/accordion.php',$accordion_args);

    }

//    function ul_to_array($ul)
//    {
//
//        $dom = new DOMDocument();
//        $dom->loadHTML($ul);
//
//        $ul = $dom->firstChild;
//
//        foreach ($ul->childNodes as $childNode) {
//
//            if($childNode->childNodes->length){
//
//            }
//        }
//
//
//
//    }


    protected function get_css_classes()
    {
        return self::CSS_CLASSES;
    }
}