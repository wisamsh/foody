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
                    if(query.length > 2) {
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
                            if(splitedUrl.length > 1 && splitedUrl[1] !== ''){
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
            window.location.href =  window.location.origin + '/?s=' + $('input[name="s"]', $prev).val();
        }
    });

    $autocompletInput.keydown((e) => {
        let key = e.which;
        if (key === 13) {
            let search = $(e.target).val();
            let url = get_suggestion(search);
            if(url != ''){
                window.location = url;
            }
            else if (search) {
                window.location = '/?s=' + search;
            }
            e.preventDefault();
            return false;
        }

    });
};

function get_suggestion(searchString) {
    let suggestions  = $('.foody-search-suggestion');
    let url = '';
    for(let i = 0; i < suggestions.length; i++){
        if(searchString == suggestions[i].innerText){
            url = $((suggestions[i].children)[0]).attr('href');
            break;
        }
    }
    return url;
}