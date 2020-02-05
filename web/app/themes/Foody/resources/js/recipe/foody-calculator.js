/**
 * Created by moveosoftware on 7/10/18.
 */
// window.Fraction = require('fractional').Fraction;
window.calculator = function (selector) {

    $elements = $(selector);

    if ($elements.length == 0) {
        return;
    }

    let $numberOfDishes = $('#number-of-dishes');

    let subIng = $('.substitute-ingredient');

    let originalNumberOfDishes = parseInt($numberOfDishes.data('amount'));

    if (foodyGlobals.isMobile) {
        $numberOfDishes.on('click', function (e) {
            $(this).val(null);
        });
    }

    subIng.on('click',function () {
       let ingredientToSub = $(this).closest('.ingredients').find('.ingredient');
       let ingredientAmounts = $(ingredientToSub).find('.amount');
       let ingredientToSubTitle = ingredientToSub.find('.foody-u-link');
       let newIngredientTitle = '';

       /** swap data attributes **/
        ingredientAmounts.toArray().forEach(function (amount) {
            newIngredientTitle = substituteDataAttr($(amount));
        });

        this.innerText = 'החלפה ל' + ingredientToSubTitle[0].innerText;
        ingredientToSubTitle[0].innerText = newIngredientTitle;

        $.each(ingredientAmounts, function (index) {
            ingredientAmounts[index].innerText = $(ingredientAmounts[index]).attr('data-amount');
        });

        let currentNumberOfDishes = $($numberOfDishes).val();
        updateNutrients(originalNumberOfDishes, currentNumberOfDishes, false);
    });

    $numberOfDishes.on('input', function () {
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

        let originalSlices = $(this).find('option[data-original=1]').data('slices');
        if (!originalSlices) {
            originalSlices = 1;
        }
        let slices = $option.data('slices');
        if (!slices) {
            slices = 1;
        }
        updateIngredients($elements, originalSlices, slices, original);

    });


};


function updateIngredients($elements, originalNumberOfDishes, val, reset) {

    updateNutrients(originalNumberOfDishes, val, reset);

    $elements.each(function () {

        let $this = $(this);
        let base = $this.data('amount') / originalNumberOfDishes;

        let plural = $this.data('plural');
        let singular = $this.data('singular');
        let unit = $this.data('unit');
        if (!unit) {
            unit = '';
        }

        let calculated = base * val;
        let text = prettyNumber(calculated);

        let name = singular;

        if (Math.ceil(parseFloat(text)) > 1 || (Math.ceil(parseFloat(text)) > 0 && unit == 'ק"ג')) {
            if (plural) {
                name = plural;
            }
        }

        // noinspection EqualityComparisonWithCoercionJS
        if (val == originalNumberOfDishes || reset) {
            text = $this.data('original');
        }

        let $name = $('span.name a', $this.parent());


        $name.text(name);
        if (parseFloat(text) > 0) {
            $this.text(text);
        }
    });
}

function updateNutrients(originalNumberOfDishes, val, dontChange = true, reset ) {
    // Update header amount title
    $('.nutrients-header-dishes-amount').text(val);

    $('.nutrition-row').each(function () {

        let $this = $(this);
        let nutrient = $this.data('name');
        let original = $this.data('original');
        let nutrientBaseValue = 0;

        val = parseInt(val);
        let totalValueForNutrient = 0;
        originalNumberOfDishes = parseInt(originalNumberOfDishes);
        if ((val === originalNumberOfDishes || reset) && dontChange) {
            totalValueForNutrient = parseFloat(original);
        } else {
            $('.ingredients .amount').each(function () {
                nutrientBaseValue = $(this).attr(`data-${nutrient}`);
                console.log('nutrientBaseValue ', nutrientBaseValue);
                if (!nutrientBaseValue) {
                    nutrientBaseValue = 0;
                }

                nutrientBaseValue = parseFloat(nutrientBaseValue);

                // Divide by original num of dishes to retrieve one dish
                nutrientBaseValue = nutrientBaseValue / originalNumberOfDishes;

                // Dish value times new val
                if (nutrientBaseValue) {
                    nutrientBaseValue = nutrientBaseValue * val;
                }

                if (nutrientBaseValue) {
                    totalValueForNutrient += nutrientBaseValue;
                }

                console.log('totalValueForNutrient for ' + nutrient, totalValueForNutrient);
            });
        }
        let decimals = 1;
        if (nutrient === 'protein') {
            decimals = 1;
        }

        if (totalValueForNutrient > 0) {
            $('.chosen-dishes-nutrition .value', this).text(prettyNumber(totalValueForNutrient, decimals));
            if(!dontChange){
                $('.dish-nutrition .value', this).text(round(totalValueForNutrient/val,1), decimals)
            }
        }

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

function round(value, precision) {
    let multiplier = Math.pow(10, precision || 0);
    return Math.round(value * multiplier) / multiplier;
}

function substituteDataAttr(amount) {
    const prefix = 'substitute';
    let dataAttributes = amount.data();
    for (const property in dataAttributes) {
        if(property.indexOf(prefix) != 0) {
            swapSubstituteAtrr(property, amount, prefix);
        }
    }
    return amount.attr('data-singular');
}

function swapSubstituteAtrr(property, amount, prefix) {
    let temp = amount.attr('data-'+ property);
    amount.attr('data-'+property, amount.attr('data-'+prefix+'-'+property));
    amount.attr('data-'+prefix+'-'+property, temp);
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
