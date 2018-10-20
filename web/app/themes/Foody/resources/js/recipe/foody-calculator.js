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


        $elements.each(function () {

            let $this = $(this);
            let base = $this.data('amount') / originalNumberOfDishes;

            let calculated = base * val;
            let text = calculated.toFixed(2);
            if (val == originalNumberOfDishes) {
                text = $this.data('original');
            }

            $this.text(text);
        })
    });


};
