<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:32 PM
 */
class Foody_Ingredient extends Foody_Post
{

    public $amount;

    public $unit;


    private $units = array(
        'grams' => 'גרם',
        'cups' => 'כוסות',
        'spoons' => 'כפות'
    );

    /**
     * Foody_Ingredient constructor.
     * @param $ingredient_post_id
     * @param $amount
     * @param $unit
     */
    public function __construct($post, $amount, $unit)
    {
        parent::__construct($post);
        $this->amount = $amount;
        $this->unit = $unit;
    }


    function __toString()
    {
        return sprintf(
            '%s %s %s',
            $this->amount,
            $this->getUnit(),
            $this->getTitle()
        );
    }

    public function getUnit()
    {
        return $this->units[strtolower($this->unit)];
    }


}