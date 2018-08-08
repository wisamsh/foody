<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 7:17 PM
 */
abstract class Foody_Post
{

    public $id;

    private $posted_on;

    protected $image;

    private $description;

    private $description_mobile;

    private $title;

    private $author_image;

    private $author_name;

    private $view_count;

    public $body;

    public $link;

    public $favorite = false;


    protected $post;

    protected $stub_images = array();

    /**
     * FoodyPost constructor.
     * @param WP_Post $post
     */
    public function __construct(WP_Post $post = null)
    {

        $this->stub_images = array(
            'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/Nimrod_Genisher_0272.jpg',
            'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/Nimrod_Genisher_0321.jpg',
            'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/Nimrod_Genisher_0032.jpg'
        );

        if ($post != null) {
            $this->post = $post;
            $this->id = $post->ID;
            global $wp_session;
            if (isset($wp_session['favorites']) && in_array($this->id, $wp_session['favorites'])) {
                $this->favorite = true;
            }
            $a = get_the_post_thumbnail($this->id,'foody-main');
            $this->image = get_the_post_thumbnail_url($this->id ,'foody-main');
            $this->posted_on = foody_posted_on(false, $post);
            $this->description = get_the_excerpt($this->post->ID);
            $this->description_mobile = get_field('mobile_caption', $this->post->ID);
            $this->title = get_the_title($post->ID);
            $this->view_count = view_count_display(foody_get_post_views($post->ID), 0);
            $this->author_image = get_the_author_meta('wp_user_avatars', get_the_author_meta('ID'))['90'];
            $this->author_name = foody_posted_by(false);
            $this->body = apply_filters('the_content', $post->post_content);
            $this->link = get_permalink($post->ID);

        } else {
            $k = array_rand($this->stub_images);
            $v = $this->stub_images[$k];
            $this->image = $v;// $GLOBALS['images_dir'] . 'food.jpg';
            $this->posted_on = date('d.m.y');
            $this->description = 'המנה המושלמת לאירוח, קלה ולעולם לא מאכזבת. הטעם המושלם של תפוחי אדמה בתנור עם טוויסט מיוחד.';
            $this->title = 'סירות תפוחי אדמה אפויות';
            $this->view_count = view_count_display(13454, 1);
            $this->author_image = 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/avatar_user_2_1527527183-250x250.jpg';// $GLOBALS['images_dir'] . 'matan.jpg';
            $this->author_name = "ישראל אהרוני";
            $this->link = get_permalink();
        }
    }

    /**
     * @return string
     */
    public function getAuthorImage(): string
    {
        if ($this->author_image == null) {
            $this->author_image = '';
        }
        return $this->author_image;
    }

    /**
     * @param string $author_image
     */
    public function setAuthorImage(string $author_image)
    {
        $this->author_image = $author_image;
    }


    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }

    /**
     * @param mixed $author_name
     */
    public function setAuthorName($author_name)
    {
        $this->author_name = $author_name;
    }

    /**
     * @return string
     */
    public function getViewCount(): string
    {
        return $this->view_count;
    }

    /**
     * @param string $view_count
     */
    public function setViewCount(string $view_count)
    {
        $this->view_count = $view_count;
    }

    /**
     * @return mixed
     */
    public function getPostedOn()
    {
        return $this->posted_on;
    }

    /**
     * @param mixed $posted_on
     */
    public function setPostedOn($posted_on)
    {
        $this->posted_on = $posted_on;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if (wp_is_mobile()) {
            return $this->description_mobile;
        }
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function get_primary_category()
    {
        if ($this->post != null) {
            $term_list = wp_get_post_terms($this->post->ID, 'category', array("fields" => "names"));
            foreach ($term_list as $term) {
                if (get_post_meta($this->post->ID, '_yoast_wpseo_primary_category', true) == $term->term_id) {
                    return $term;
                }
            }
            return $term_list[0];
        }

        return null;
    }

    public abstract function the_featured_content();

    public abstract function the_sidebar_content();
}