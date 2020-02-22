/**
 * Created by moveosoftware on 9/27/18.
 */


module.exports = function (selector, options) {
    let $autocompletInput = $(selector);

    var searchRequest;
    var currentQuery;

    let defaultOptions = {
        hint: false,
        openOnFocus: true,
        cssClasses: {
            prefix: 'foody',
            suggestions: 'search-suggestions',
            suggestion: 'search-suggestion'
        }
    };

    defaultOptions = _.extend(defaultOptions, options);


    $autocompletInput.autocomplete(
        defaultOptions,
        [
            {
                source: function (query, cb) {
                    currentQuery = query;
                    if (query.length > 2) {
                        try {
                            searchRequest.abort();
                        } catch (e) {
                        }
                        searchRequest = $.post('/wp/wp-admin/admin-ajax.php', {
                            search: query,
                            action: 'search_site'
                        }, function (res) {
                            cb(res.data);
                        });
                    }
                },
                displayKey: 'name',
                templates: {
                    suggestion: function (suggestion) {
                        let link = '';
                        if (suggestion.name != null) {
                            let splitedUrl = suggestion.link.split('?');
                            let autocompleteAnalyticsParam = '?auto=1';
                            if (splitedUrl.length > 1 && splitedUrl[1] !== '') {
                                autocompleteAnalyticsParam = '&auto=1';
                            }
                            let name = suggestion.name.replace(new RegExp('(' + currentQuery + ')', 'g'), '<span>$1</span>');
                            link = '<a href="' + suggestion.link + autocompleteAnalyticsParam + '">' + name + '</a>';
                        }

                        return link;
                    }
                }
            }
        ]);


    $('.search-bar label').on('click', function () {
        let $prev = $(this).prev();
        let searchTerm = $('input[name="s"]', $prev).val();
        if (searchTerm && searchTerm.trim()) {
            window.location.href = window.location.origin + '/?s=' + $('input[name="s"]', $prev).val();
        }
    });

    $autocompletInput.keydown((e) => {
        let key = e.which;
        if (key === 13) {
            let search = $(e.target).val();
            let url = get_suggestion_url(search);
            if (url != '') {
                let searchSuggestion = get_suggestion(search)[0].innerText;
                let amount = $('.foody-search-suggestions')[0].childNodes.length;

                set_search_order('searches_strings', search);
                eventCallback('', 'חיפוש', 'בחירה בתוצאה מוצעת', searchSuggestion, 'מספר חיפוש', get_search_order('searches_strings', search), amount, '', 'מנגנון תוצאות');
                window.location = url;
            } else if (search) {
                set_search_order('searches_strings', search);
                eventCallback('', 'חיפוש', 'חיפוש טקסט חופשי', search, 'מספר חיפוש', get_search_order('searches_strings', search), '', '', 'חיפוש חופשי');
                window.location = '/?s=' + search;
            }
            e.preventDefault();
            return false;
        }

    });
};

function get_suggestion_url(searchString) {
    let url = '';
    let suggestion = get_suggestion(searchString);

    if (suggestion !='' && suggestion[0].innerText == searchString) {
        url = suggestion.attr('href');
    }
    return url;
}

function get_suggestion(searchString) {
    let suggestions = $('.foody-search-suggestion');
    let suggestion = '';
    for (let i = 0; i < suggestions.length; i++) {
        if (searchString == suggestions[i].innerText) {
            suggestion = $((suggestions[i].children)[0]);
            break;
        }
    }
    return suggestion;
}


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 * @param cdDesc
 * @param cdValue
 * @param _order_location
 * @param _amount
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', _amount = '', _order_location = '', _object, recipe_details = '') {

    /**
     * Recipe name
     */
    let recipe_name = recipe_details != '' ? recipe_details['title'] : '';

    /**
     * Item category
     */
    let item_category = '';

    /**
     * Chef Name
     */
    let chef = recipe_details != '' ? recipe_details['author'] : '';

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    /**
     * Difficulty Level
     */
    let difficulty_level = '';

    /**
     * Preparation Time
     */
    let preparation_time = 0;

    /**
     * Ingredients Count
     */
    let ingredients_amount = 0;

    /**
     * Index of recipe in current Session
     */
    let order_location = _order_location;

    /**
     * Recipe view count
     */
    let amount = _amount;

    /**
     * Has rich content - does contains video or product buy option
     */
    let hasRichContent = '';

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        recipe_name,
        item_category,
        chef,
        difficulty_level,
        preparation_time,
        ingredients_amount,
        order_location,
        amount,
        hasRichContent,
        cdDesc,
        cdValue,
        '',
        _object
    );
}

function getRecipeLocationFromParent($parent) {
    let dataset = $parent;
    var flag = true;
    while ((flag)) {
        if (jQuery.inArray('grid-item', dataset.className.split(' ')) == -1) {
            dataset = dataset.parentNode;
        } else {
            flag = false;
        }
    }
    return dataset;
}

function getCurrentRecipeDetail(recipeContainer) {
    let recipeDetails = {};

    //get recipe title
    let titleElement = $(recipeContainer).find('.grid-item-title');
    if (titleElement.length) {
        recipeDetails['title'] = titleElement[0] ? titleElement[0].innerText : '';
    }

    //get recipe author
    let authorElementContainer = $(recipeContainer).find('.recipe-item-details');
    if (authorElementContainer.length) {
        let authorElement = $(authorElementContainer).find('li > a');
        if (authorElement.length) {
            recipeDetails['author'] = authorElement[0] ? authorElement[0].innerText : '';
        }
    }

    //has video
    let videoDurationContainer = $(recipeContainer).find('.duration');
    if (videoDurationContainer.length) {
        recipeDetails['has_rich_content'] = true;
    }
    return recipeDetails;
}

function set_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        searches_comitted = [];
    }

    if (!searches_comitted.includes(key)) {
        searches_comitted.push(key);
    }
    sessionStorage.setItem(action, JSON.stringify(searches_comitted));
}

function get_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        return 0;
    }
    return jQuery.inArray(key, searches_comitted) + 1;
}