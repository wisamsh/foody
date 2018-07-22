<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/10/18
 * Time: 4:59 PM
 */
class Foody_Profile
{

    private $sidebar_filter;

    private $foody_user;

    private $grid;

    /**
     * Foody_Profile constructor.
     */
    public function __construct()
    {
        $this->sidebar_filter = new SidebarFilter();
        $this->foody_user = new Foody_User();
        $this->grid = new RecipesGrid();
    }

    public function sidebar()
    {
        dynamic_sidebar('foody-sidebar');
    }

    public function get_image()
    {
        return get_user_meta(get_current_user_id(), 'wp_user_avatars', true)['90'];
    }

    public function get_name()
    {
        $first = $this->foody_user->user->first_name;
        $last = $this->foody_user->user->last_name;
        return sprintf('%s %s', $first, $last);
    }

    public function get_email()
    {
        return $this->foody_user->user->user_email;
    }

    public function my_recipes()
    {
        $this->grid->grid_debug(12, 2);
    }

    public function my_channels_recipes()
    {
        $this->grid->grid_debug(9, 2);
    }

    public function my_channels()
    {
        $list = $this->foody_user->get_favorite_channels();


        $list = array(
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            ),
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            ),
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            ),
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            ),
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            ),
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            ),
            array(
                'name' => 'שם שם',
                'image' => 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/06/matan-90x90.jpg'
            )
        );

        foody_get_template_part(
            get_template_directory() . '/template-parts/content-user-managed-list.php',
            $list
        );
    }
}