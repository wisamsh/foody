/**
 * Created by moveosoftware on 10/7/18.
 */

let FoodyGrid = require('./foody-grid');
let FoodyLocationUtils = require('./foody-location-utils');

module.exports = (function () {


    function FoodySearchFilter(settings) {
        this.init(settings);
    }

    /**
     * Init properties and set initial
     * filter.
     * Sets a change listener on filter checkboxes
     * */
    FoodySearchFilter.prototype.init = function (settings) {
        this.settings = settings;
        this.locationUtils = new FoodyLocationUtils();
        this.searchFilter = {};
        let defaultGridArgs = {selector: settings.grid};
        if (settings.gridArgs) {
            defaultGridArgs = _.extend(defaultGridArgs, settings.gridArgs);
        }

        this.grid = new FoodyGrid(defaultGridArgs);

        if (!settings.selector) {
            settings.selector = '#accordion-foody-filter';
        }

        this.cols = settings.cols;
        this.$filter = $(settings.selector);

        if (this.$filter.length) {
            this.buildInitialFilter();
            this.attachChangeListener();
            if (!settings.searchButton) {
                settings.searchButton = '.show-recipes';
            }

            let that = this;
            $(settings.searchButton, $(settings.page)).on('click', function () {
                that.doQuery.call(that);
            });

        }
    };

    FoodySearchFilter.prototype.attachChangeListener = function () {
        let pageContainer = this.settings.page;
        let $checkboxes = $('aside input[type="checkbox"]', pageContainer);
        let that = this;
        $checkboxes.on('change', function (e) {
            if (that.isLoading) {
                return;
            }

            e.preventDefault();

            let data = $(this).data();
            let groupKey = $(this).closest('.foody-accordion').attr('id');
            let key = `${groupKey}_${data.type}_${this.name}`;
            if (this.checked) {
                that.searchFilter[key] = that.getParsedInput(this);
            } else {
                if (that.searchFilter[key]) {
                    delete that.searchFilter[key];
                }
            }

            if (foodyGlobals.isMobile === false || (foodyGlobals.isTablet && $(document).width() >= 1024)) {
                that.doQuery();
            }

            return false;
        });
    };

    FoodySearchFilter.prototype.loading = function () {
        this.isLoading = true;
        this.toggleCheckboxes(true);
        this.grid.loading();
    };

    FoodySearchFilter.prototype.stopLoading = function () {
        this.isLoading = false;
        this.toggleCheckboxes(false);
        this.grid.stopLoading();
    };

    FoodySearchFilter.prototype.toggleCheckboxes = function (disable) {
        let attr = disable ? 'disable' : null;
        let $checkboxes = $('aside .md-checkbox', this.settings.page);
        $checkboxes.attr('disabled', attr);
    };

    FoodySearchFilter.prototype.buildInitialFilter = function () {

        let $checkboxes = $('aside .foody-search-filter input[type="checkbox"]', this.settings.page);
        let that = this;

        foodyGlobals.queryArgs = foodyGlobals.queryArgs || {};

        this.searchFilter = {};

        let key = foodyGlobals.filterQueryArg;

        this._updateFilterByQuery(this.locationUtils.getQuery(key));

        $checkboxes.each(function () {
            if (this.checked) {
                that.searchFilter[this.name] = that.getParsedInput(this);
            }
        });
    };

    FoodySearchFilter.prototype._updateFilterByQuery = function (valuesStr) {
        if (valuesStr) {
            let that = this;
            valuesStr.split(',').forEach((value) => {
                $('aside .foody-search-filter .md-checkbox label:visible', that.settings.page).filter(function () {
                    return $(this).text().trim() === value.trim();
                }).each(function () {
                    let $checkbox = $(this).prev('input[type="checkbox"]');
                    $checkbox.prop('checked', true);
                });
            });
        }
    };

    FoodySearchFilter.prototype.getParsedInput = function (checkbox) {

        let data = $(checkbox).data();
        return {
            type: data.type,
            exclude: data.exclude,
            value: data.value
        };
    };

    FoodySearchFilter.prototype.prepareFilterForQuery = function (search) {

        /*
         * {
         *  search:'search term',
         *  'types':[{
         *      type:'categories|ingredients|techniques|accessories|limitations|tags',
         *      exclude:false,
         *      id:8
         *  }]
         * }
         * */
        search = this.locationUtils.getQuery('s');
        let args = {
            search: search,
            types: [],
            context: this.settings.context,
            context_args: this.settings.contextArgs,
        };

        for (let key in this.searchFilter) {
            if (this.searchFilter.hasOwnProperty(key)) {
                args.types.push(this.searchFilter[key]);
            }
        }

        return args;

    };

    FoodySearchFilter.prototype.doQuery = function () {

        let action = 'foody_filter';

        let ajaxSettings = {
            action: action,
            data: {
                data: this.prepareFilterForQuery(),
                options: {
                    cols: this.cols
                }
            }
        };

        if (this.settings.onQuery && typeof  this.settings.onQuery === 'function') {
            this.settings.onQuery();
        }
        let that = this;
        that.loading();
        foodyAjax(ajaxSettings, function (err, data) {
            that.stopLoading();
            if (err) {
                console.log('err: ' + err);
            } else {
                that.grid.refresh(data.data, ajaxSettings.data.data.types.length === 0);
                that._updateUri();
            }
        });
    };

    FoodySearchFilter.prototype._updateUri = function () {

        let pageContainer = this.settings.page;
        let $checkedCheckboxes = $('aside .foody-search-filter input[type="checkbox"]:checked', pageContainer);

        let urlParams = new URLSearchParams();

        // noinspection JSUnresolvedVariable
        let queryArg = foodyGlobals.filterQueryArg;

        let filterItems = $checkedCheckboxes.toArray().map((el) => {
            let $label = $(`label[for="${el.id}"]`);
            let filterTitle = '';
            if ($label.length) {
                filterTitle = $label.text().replace(/\s+/g, ' ');
                filterTitle = filterTitle.trim();
            }
            return filterTitle;
        });

        filterItems = filterItems.filter((item) => item);

        if (filterItems && filterItems.length) {
            urlParams.set(queryArg, filterItems.join(','));
        } else {
            urlParams.delete(queryArg);
        }

        this.locationUtils.updateHistory(null, urlParams.toString())

    };

    return FoodySearchFilter;

})();