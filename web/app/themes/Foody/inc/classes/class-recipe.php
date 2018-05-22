<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:23 PM
 */
class Recipe extends FoodyPost
{

    private $author_image;

    private $author_name;

    private $view_count;

    private $duration;


    /**
     * Recipe constructor.
     */
    public function __construct(WP_Post $post = null)
    {
        parent::__construct($post);

        $this->duration = 2.45;
        $this->view_count = view_count_display(13454,1);
        $this->author_image = $GLOBALS['images_dir'] . 'matan.jpg';
        $this->author_name = "ישראל אהרוני";
    }

    /**
     * @return string
     */
    public function getAuthorImage(): string
    {
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
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration(float $duration)
    {
        $this->duration = $duration;
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


    public function parse(WP_Post $post)
    {
        // TODO: Implement parse() method.
    }
}