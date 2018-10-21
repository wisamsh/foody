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
    $elements.each(function () {

        let $this = $(this);
        let base = $this.data('amount') / originalNumberOfDishes;

        let calculated = base * val;
        let text = calculated.toFixed(2);
        if (val == originalNumberOfDishes || reset) {
            text = $this.data('original');
        }

        $this.text(text);
    })
}
