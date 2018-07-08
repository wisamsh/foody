<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 7:17 PM
 */
abstract class Foody_Post
{

    private $posted_on;

    protected $image;

    private $description;

    private $title;


    protected $post;

    protected $stub_images = array(
//		'http://localhost:8000/app/uploads/2018/05/Nimrod_Genisher_0142.jpg',
        'http://localhost:8000/app/uploads/2018/05/Nimrod_Genisher_0272.jpg',
        'http://localhost:8000/app/uploads/2018/05/Nimrod_Genisher_0321.jpg',
        'http://localhost:8000/app/uploads/2018/05/Nimrod_Genisher_0032.jpg'
    );

    /**
     * FoodyPost constructor.
     * @param WP_Post $post
     */
    public function __construct(WP_Post $post = null)
    {
        if ($post != null) {
            $this->post = $post;
            $this->image = get_post_thumbnail_id();
            $this->posted_on = foody_posted_on(false);
            $this->description = get_the_excerpt();
            $this->title = get_the_title($post->ID);

        } else {
            $k = array_rand($this->stub_images);
            $v = $this->stub_images[$k];
            $this->image = $v;// $GLOBALS['images_dir'] . 'food.jpg';
            $this->posted_on = date('d.m.y');
            $this->description = 'המנה המושלמת לאירוח, קלה ולעולם לא מאכזבת. הטעם המושלם של תפוחי אדמה בתנור עם טוויסט מיוחד.';
            $this->title = 'סירות תפוחי אדמה אפויות';
        }
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
}