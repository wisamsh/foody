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
                        let link = '';
                        if (suggestion.name != null) {
                            let name = suggestion.name.replace(new RegExp('(' + currentQuery + ')', 'g'), '<span>$1</span>');
                            link = '<a href="' + suggestion.link + '">' + name + ' </a>';
                        }

                        return link;
                    }
                }
            }
        ]);


    $('.search-bar label').on('click', function () {
       let $prev = $(this).prev();
        window.location.href = '?s=' + $('input[name="s"]',$prev).val();
    });

};