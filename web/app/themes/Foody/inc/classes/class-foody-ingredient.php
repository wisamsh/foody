<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:32 PM
 */

use Phospr\Fraction;

class Foody_Ingredient extends Foody_Post
{

    public $amount;

    public $unit;

    public $amounts;

    public $amounts_delimiter = ' <b> או </b> ';

    public $unit_taxonomy;

    public $fractions = [
        '3' => '1/3',
        '25' => '1/4',
        '5' => '1/2',
        '66' => '2/3',
        '75' => '3/4',
    ];

    public $plural_name;

    public $singular_name;

    /**
     * Foody_Ingredient constructor.
     * @param $ingredient_post_id
     * @param $amount
     * @param $unit
     */
    public function __construct($post, $amount = null, $unit = null)
    {
        parent::__construct($post);
        $this->amount = $amount;
        $this->unit = $unit;

        $this->plural_name = get_field('plural_name', $this->id);
        $this->singular_name = $this->getTitle();

    }


    function __toString()
    {
        return sprintf(
            '%s %s %s',
            $this->get_the_amounts(),
            $this->getUnit(),
            $this->getTitle()
        );
    }

    public function getUnit()
    {
        return $this->unit;
    }


    public function the_featured_content()
    {
        // TODO: Implement the_featured_content() method.
    }

    public function the_sidebar_content($args = array())
    {
        // TODO: Implement the_sidebar_content() method.
    }

    public function the_amounts($echo = true)
    {
        if ($this->amounts != null) {

            $length = count($this->amounts);

            $last = array_pop($this->amounts);

            $to_fraction = array($this, 'to_fraction');
            $get_ingredient_data_attr = array($this, 'get_ingredient_data_attr');

            $content = implode($this->amounts_delimiter, array_map(function ($amount) use ($to_fraction, $get_ingredient_data_attr) {

                $display = call_user_func($to_fraction, $amount['amount']);
                $data = call_user_func($get_ingredient_data_attr, $amount['amount'], $display);
                return
                    '<span dir="ltr" class="amount"' . $data . '>
                        ' . $display . '
                    </span>
                    <span class="unit">
                         ' . $amount['unit'] . '
                    </span>';
            }, $this->amounts));

            $unit_tax = $last['unit_tax'];

            $show_after_ingredient = get_field('show_after_ingredient', $unit_tax);


            $display = $this->to_fraction($last['amount']);

            $amount = $last['amount'];

            $title = $this->getTitle();

            $unit = $last['unit'];

            $ing_html = $this->get_ingredient_html($amount, $display, $unit, $title, $show_after_ingredient);


            if ($length > 1) {
                $content .= $this->amounts_delimiter;
            }

            if (!empty($content)) {
                $content .= $ing_html;
            } else {
                $content = $ing_html;
            }


        } else {
            $content = '<span class="unit">
                        ' . $this->getTitle() . '
                    </span>';
        }

        if ($echo) {
            echo $content;
        }
        return $content;
    }

    private function get_ingredient_html($amount, $display, $unit, $title, $is_unit_after_title)
    {

        if (!empty($this->plural_name)) {
            if (ceil($amount) > 1) {
                $title = $this->plural_name;
            }
        }

        $data = $this->get_ingredient_data_attr($amount, $display);

        $amount_el = ' <span dir="ltr" class="amount" ' . $data . '>
                        ' . $display . '
                    </span>';

        $unit_el = ' <span class="unit">
                         ' . $unit . '
                    </span>';

        $name_el = '<span class="name">
                        ' . $title . '
                    </span>';

        if ($is_unit_after_title) {
            $amount_el .= $name_el;
            $amount_el .= $unit_el;
        } else {
            $amount_el .= $unit_el;
            $amount_el .= $name_el;
        }

        return $amount_el;
    }


    private function get_ingredient_data_attr($amount, $display)
    {
        return foody_array_to_data_attr([
            'amount' => $amount,
            'original' => $display,
            'plural' => $this->plural_name,
            'singular' => $this->singular_name
        ]);
    }


    private function to_fraction($dec)
    {

        $str = strval($dec);

        $fraction = explode('.', $str);
        if (isset($fraction[1])) {


            if ($fraction != null) {
                $whole = $fraction[0];
                if ($whole == '0') {
                    $whole = '';
                }
                return $whole . ' ' . $this->fractions[$fraction[1]];
            }
        }

        return $dec;
        // return Fraction::fromFloat($dec)

    }

    public function get_the_amounts()
    {
        return $this->the_amounts(false);
    }

    public function the_details()
    {
        // TODO: Implement the_details() method.
    }
}