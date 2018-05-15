<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/15/18
 * Time: 4:54 PM
 */
class FoodyTeam
{


    /**
     * FoodyTeam constructor.
     */
    public function __construct()
    {

    }

    public function list_authors()
    {
        $args = array(
            'role'    => 'author',
            'orderby' => 'user_nicename',
            'order'   => 'ASC'
        );

        $authors = get_users( $args );

        foreach ($authors as $author) {

//            get_user_meta($author->ID,'avatar');
//            get_user_meta($author->ID,'display_name');

            $image = get_the_author_meta('wp_user_avatars',$author->ID)['96'];
            $name =  get_the_author_meta('display_name',$author->ID);

            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
            include(locate_template('author.php'));
        }

    }
}