/**
 * Created by moveosoftware on 7/10/18.
 */

window.calculator = function (selector) {

    $elements = $(selector);

    if ($elements.length == 0) {
        return;
    }

    let $numberOfDishes = $('#number-of-dishes');

    let originalNumberOfDishes = parseInt($numberOfDishes.data('amount'));

    $numberOfDishes.on('change keyup paste', function () {

        let val = $(this).val();

        if (originalNumberOfDishes <= 0 || val <= 0) {
            return;
        }


        updateIngredients($elements, originalNumberOfDishes, val);

    });


    $('#pan-conversions').on('changed.bs.select', function () {
        let val = $(this).val();


        let $option = $(this).find(':selected');

        let original = $option.data('original');

        updateIngredients($elements, 1, val, original);

    });

};


function updateIngredients($elements, originalNumberOfDishes, val, reset) {

    updateNutrients(originalNumberOfDishes, val, reset);

    $elements.each(function () {

        let $this = $(this);
        let base = $this.data('amount') / originalNumberOfDishes;

        let plural = $this.data('plural');
        let singular = $this.data('singular');

        let calculated = base * val;
        let text = prettyNumber(calculated);

        let name = singular;
        if (Math.ceil(parseFloat(text)) > 1) {
            if (plural) {
                name = plural;
            }
        }

        if (val === originalNumberOfDishes || reset) {
            text = $this.data('original');
        }

        let $name = $('span.name', $this.parent());


        $name.text(name);

        $this.text(text);
    });
}

function updateNutrients(originalNumberOfDishes, val, reset) {
    $('.nutrition-row').each(function () {

        let $this = $(this);
        let nutrient = $this.data('name');
        let original = $this.data('original');

        let totalValueForNutrient = 0;

        if (val === originalNumberOfDishes || reset) {
            totalValueForNutrient = parseFloat(original);
        } else {
            $('.ingredients .amount').each(function () {
                let nutrientBaseValue = $(this).data(nutrient);
                if (!nutrientBaseValue) {
                    nutrientBaseValue = 0;
                }

                nutrientBaseValue = parseFloat(nutrientBaseValue);

                nutrientBaseValue = nutrientBaseValue * val;


                totalValueForNutrient += nutrientBaseValue;
            });
        }
        let decimals = 0;
        if (nutrient === 'protein') {
            decimals = 1;
        }

        $('.value', this).text(prettyNumber(totalValueForNutrient,decimals))

    });
}

function prettyNumber(num, decimals) {
    if (decimals === undefined) {
        decimals = 2;
    }
    let text = num.toFixed(decimals);

    let number = String(text).split('.');
    if (number.length === 2) {
        let decimal = number[1];
        if (decimal === '00') {
            text = number[0];
        }
    }

    return text;
}
