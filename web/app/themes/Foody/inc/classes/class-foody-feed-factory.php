<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 7:32 PM
 */
class Foody_FeedFactory
{


    /**
     * Foody_FeedFactory constructor.
     */
    public function __construct()
    {

    }


    /**
     * @param IFoody_ListItem[] $list_items
     */
    public function the_feed($list_items)
    {

        foreach ($list_items as $list_item) {
            $list_item -> item();
        }

    }
}