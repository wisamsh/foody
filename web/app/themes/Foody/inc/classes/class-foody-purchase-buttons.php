<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 1:46 PM
 */

class Foody_PurchaseButtons
{


    /**
     * @var $options
     *
     *
     */
    private $options;

    private static $instance;

    /**
     * Foody_PurchaseButtons constructor.
     */
    private function __construct()
    {
        $this->load_options();
    }

    /**
     * @return Foody_PurchaseButtons
     */
    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new Foody_PurchaseButtons();
        }

        return self::$instance;
    }

    public static function the_button($button, $echo = true)
    {
        if (!empty($button['image'])) {
            $images = [
               'mobile_image' =>  $button['image']
            ];
            if (!empty($button['desktop_image'])) {
                $images['image'] = $button['desktop_image'];
            }

            $content = foody_get_template_part(
                get_template_directory() . '/template-parts/common/picture.php',
                ['return' => true, 'images' => $images]
            );
        } else {
            $color = $button['color'];
            $text_color = $button['title_color'];
            $title = $button['title'];

            $content = "<button style='background-color: $color;color: $text_color;'>$title</button>";
        }

        if ($echo) {
            echo $content;
        }

        return $content;
    }

    /**
     * @param int $post
     * @return array purchase buttons relevant to post
     */
    public function get_buttons_for_post($post)
    {
        $buttons = [];

        // only allow button with defined filters
        $options = array_filter($this->options, function ($option) {
            return !empty($option['filters_list']);
        });

        foreach ($options as $option) {

            // only first filters section
            // is relevant
            $args = [
                'types' => SidebarFilter::parse_search_args($option['filters_list'][0])
            ];

            // purchase_buttons will invoke purchase_buttons ffn
            // in class Foody_Query
            $foody_search = new Foody_Search('purchase_buttons', ['id' => $post]);

            $result = $foody_search->query($args);

            // $post exists in query, add
            // button options to buttons
            if (!empty($result['posts'])) {
                $copy = array_merge_recursive([], $option);
                unset($copy['filters_list']);
                $buttons[] = $copy;
            }
        }

        return $buttons;

    }

    /*
     *
        [
          {
            "title": "כפתור",
            "link": {
              "title": "",
              "url": "http://foody.co.il/%d7%90%d7%99%d7%99%d7%98%d7%9e%d7%99%d7%9d/",
              "target": ""
            },
            "color": "#26a88e",
            "image": false,
            "desktop_image": false,
            "filters_list": [
              {
                "title": "",
                "type": "categories",
                "values": [
                  {
                    "title": "",
                    "value": 33,
                    "exclude": true,
                    "value_group": {
                      "type": [],
                      "value": false
                    },
                    "switch_type": false
                  }
                ],
                "exclude_all": false
              }
            ]
          }
        ]
     * */
    private function load_options()
    {
        $options = get_field('buttons', 'foody_purchase_options');
        $this->options = $options;
    }
}