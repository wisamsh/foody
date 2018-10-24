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
                },
                displayKey: 'name',
                templates: {
                    suggestion: function (suggestion) {
                        console.log(suggestion);

                        let name = suggestion.name.replace(new RegExp('(' + currentQuery + ')', 'g'), '<span>$1</span>');
                        let link = '<a href="' + suggestion.link + '">' + name + ' </a>';
                        return link;
                        // return suggestion._highlightResult.name.value;
                    }
                }
            }
        ]);

    $autocompletInput.on('autocomplete:selected', function (event, item) {

        // if (item) {
        //     window.location.search.s = item.name;
        // }

    });
};