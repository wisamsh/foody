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

    let sugarNutrient = $('[data-name=sugar]');
    let caloriesNutrient = $('[data-name=calories]');
    let sugarNutrientOriginalVal = null;
    let caloriesNutrientOriginalVal = null;
    let textToShow = '';
    let textColor = '';

    if (sugarNutrient.length) {
        sugarNutrientOriginalVal = parseFloat(sugarNutrient.data('original'));
    }

    if (caloriesNutrient.length) {
        caloriesNutrientOriginalVal = parseFloat(caloriesNutrient.data('original'));
    }

    if (foodyGlobals.isMobile) {
        $numberOfDishes.on('click', function (e) {
            $(this).val(null);
        });
    }

    subIng.on('click', function () {
            textToShow = $(this).data('text');
            let ingredientToSub = $(this).closest('.ingredients').find('.ingredient');
            let ingredientAmounts = $(ingredientToSub).find('.amount');
            let ingredientUnit = $(ingredientToSub).find('.unit');
            let ingredientToSubTitle = ingredientToSub.find('.foody-u-link');
            let newIngredientTitle = '';
            textColor = $(this).attr('data-text-color');

            /** swap data attributes **/
            ingredientAmounts.toArray().forEach(function (amount) {
                newIngredientTitle = substituteDataAttr($(amount));
            });

            this.innerText = 'החלפה ל' + ingredientToSubTitle[0].innerText;
            ingredientToSubTitle[0].innerText = newIngredientTitle;

            /** swap ingredients units and amounts **/
            $.each(ingredientAmounts, function (index) {
                if ($numberOfDishes[0].defaultValue == $numberOfDishes[0].value) {
                    ingredientAmounts[index].innerText = $(ingredientAmounts[index]).attr('data-amount');
                } else {
                    let base = $(ingredientAmounts[index]).attr('data-original') / $numberOfDishes[0].defaultValue;
                    let newAmount = base * $numberOfDishes[0].value;
                    ingredientAmounts[index].innerText = prettyNumber(newAmount);
                }
            });

            if ($numberOfDishes.length) {

                let currentNumberOfDishes = $($numberOfDishes).val();
                updateNutrients(originalNumberOfDishes, currentNumberOfDishes, false);
                updateUnits(ingredientUnit, ingredientAmounts);
            } else if ($('#pan-conversions').length) {
                let $option = $('#pan-conversions').find(':selected');

                let original = $option.data('original');

                let originalSlices = $(this).find('option[data-original=1]').data('slices');
                if (!originalSlices) {
                    originalSlices = 1;
                }
                let slices = $option.data('slices');
                if (!slices) {
                    slices = 1;
                }
                updateNutrients(originalSlices, slices, false);
                updateUnits(ingredientUnit, ingredientAmounts);
            }

            let caloriesNutrientValForCompare = typeof caloriesNutrientOriginalVal != 'undefined' ? (caloriesNutrientOriginalVal / $numberOfDishes[0].defaultValue) * $numberOfDishes[0].value : undefined;
            let sugarNutrientValForCompare = typeof sugarNutrientOriginalVal != 'undefined' ? (sugarNutrientOriginalVal / $numberOfDishes[0].defaultValue) * $numberOfDishes[0].value : undefined;

            handleSubsText(textToShow, caloriesNutrientValForCompare, sugarNutrientValForCompare, textColor);
            $elements = $('.recipe-ingredients-container li:not(.free-text-ingredients) .amount');
        }
    )
    ;

    $numberOfDishes.on('input', function () {
        let val = $(this).val();

        if (originalNumberOfDishes <= 0 || val <= 0) {
            return;
        }
        let isOriginalAmountOfDishes = $numberOfDishes[0].defaultValue == $numberOfDishes[0].value;
        let hasSubstitute = updateIngredients($elements, originalNumberOfDishes, val, undefined, !isOriginalAmountOfDishes);

        if (hasSubstitute) {
            let caloriesNutrientValForCompare = typeof caloriesNutrientOriginalVal != 'undefined' ? (caloriesNutrientOriginalVal / $numberOfDishes[0].defaultValue) * $numberOfDishes[0].value : undefined;
            let sugarNutrientValForCompare = typeof sugarNutrientOriginalVal != 'undefined' ? (sugarNutrientOriginalVal / $numberOfDishes[0].defaultValue) * $numberOfDishes[0].value : undefined;

            handleSubsText(textToShow, caloriesNutrientValForCompare, sugarNutrientValForCompare, textColor);

        }
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

        let isOriginalAmountOfDishes = $numberOfDishes[0].defaultValue == $numberOfDishes[0].value;
        let hasSubstitute = updateIngredients($elements, originalNumberOfDishes, val, undefined, !isOriginalAmountOfDishes);

        if (hasSubstitute) {
            let caloriesNutrientValForCompare = typeof caloriesNutrientOriginalVal != 'undefined' ? (caloriesNutrientOriginalVal / $numberOfDishes[0].defaultValue) * $numberOfDishes[0].value : undefined;
            let sugarNutrientValForCompare = typeof sugarNutrientOriginalVal != 'undefined' ? (sugarNutrientOriginalVal / $numberOfDishes[0].defaultValue) * $numberOfDishes[0].value : undefined;

            handleSubsText(textToShow, caloriesNutrientValForCompare, sugarNutrientValForCompare, textColor);
        }
    });


}
;


function updateIngredients($elements, originalNumberOfDishes, val, reset, dontChange = true) {
    let hasSubstitute = false;
    updateNutrients(originalNumberOfDishes, val, dontChange ,reset);

    $elements.each(function () {
        let $this = $(this);
        let base = '';
        let plural = '';
        let singular = '';
        let unit = '';
        let isSubstitute = false;

        if ($this.data('singular') == $this.attr('data-singular')) {
            base = $this.data('amount') / originalNumberOfDishes;
            plural = $this.data('plural');
            singular = $this.data('singular');
            unit = $this.data('unit');

        } else {
            base = $this.attr('data-amount') / originalNumberOfDishes;
            plural = $this.attr('data-plural');
            singular = $this.attr('data-singular');
            unit = $this.attr('data-unit');
            isSubstitute = true;
        }

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
        if ((val == originalNumberOfDishes || reset) && !isSubstitute) {
            text = $this.data('original');
        }

        let $name = $('span.name a', $this.parent());


        $name.text(name);
        if (parseFloat(text) > 0) {
            $this.text(text);
        }

        if (isSubstitute) {
            hasSubstitute = true;
        }
    });
    return hasSubstitute;
}

function updateNutrients(originalNumberOfDishes, val, dontChange = true, reset) {
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
            if (!dontChange && !$('#pan-conversions').length) {
                $('.dish-nutrition .value', this).text(round(totalValueForNutrient / val, 1), decimals)
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
        if (property.indexOf(prefix) != 0) {
            swapSubstituteAtrr(property, amount, prefix);
        }
    }
    return amount.attr('data-singular');
}

function swapSubstituteAtrr(property, amount, prefix) {
    let temp = amount.attr('data-' + property);
    amount.attr('data-' + property, amount.attr('data-' + prefix + '-' + property));
    amount.attr('data-' + prefix + '-' + property, temp);
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function updateUnits(ingredientUnit, ingredientAmounts) {
    $.each(ingredientUnit, function (index) {
        if (typeof $(ingredientAmounts[index]).attr('data-unit') != 'undefined') {
            ingredientUnit[index].innerText = $(ingredientAmounts[index]).attr('data-unit') + ' ';
        }
    })
}

function handleSubsText(textToShow, caloriesNutrientOriginalVal, sugarNutrientOriginalVal, textColor) {
    let showText = false;
    let newSugarNutrient = $('[data-name=sugar]').find('.chosen-dishes-nutrition > .value').length ? parseFloat($('[data-name=sugar]').find('.chosen-dishes-nutrition > .value')[0].innerText) : 0;
    let newCaloriesNutrient = $('[data-name=calories]').find('.chosen-dishes-nutrition > .value').length ? parseFloat($('[data-name=calories]').find('.chosen-dishes-nutrition > .value')[0].innerText) : 0;

    if (Math.round(newCaloriesNutrient) < Math.round(caloriesNutrientOriginalVal)) {
        let calCalc = Math.round(caloriesNutrientOriginalVal - newCaloriesNutrient);
        if(calCalc > 0) {
            textToShow += ' ' + calCalc + ' קלוריות ';
            showText = true;
        }
    }

    if (Math.round(newSugarNutrient) < Math.round(sugarNutrientOriginalVal)) {
        let sugCalc = Math.round(sugarNutrientOriginalVal - newSugarNutrient);
        if(sugCalc > 0) {
            textToShow += ' וגם ' + sugCalc + ' גרם סוכר ';
        }
    }

    if (showText) {
        if (!$('.difference-nutrient').length) {
            $('.recipe-ingredients-top').after('<span class="difference-nutrient" style="color:' + textColor + '">' + textToShow + '</span>');
            $('.recipe-ingredients').after('<span class="difference-nutrient" style="color:' + textColor + '">' + textToShow + '</span>');
        } else {

            $.each($('.difference-nutrient'), function (index) {
                $('.difference-nutrient')[index].innerText = textToShow;
            });
        }
    } else {
        if ($('.difference-nutrient').length) {
            $('.difference-nutrient').remove();
            textToShow = '';
        }
    }
}
