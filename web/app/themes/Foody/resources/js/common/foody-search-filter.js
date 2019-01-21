/**
 * Created by moveosoftware on 10/7/18.
 */

let FoodyGrid = require('./foody-grid');
let FoodyLocationUtils = require('./foody-location-utils');

module.exports = (function () {


    function FoodySearchFilter(settings) {
        this.init(settings);
    }

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
        let $checkboxes = $('input[type="checkbox"]', pageContainer);
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
        let $checkboxes = $('.md-checkbox', this.$filter);
        $checkboxes.attr('disabled', attr);
    };

    FoodySearchFilter.prototype.buildInitialFilter = function () {

        let $checkboxes = $('input[type="checkbox"]', this.$filter);
        let that = this;

        foodyGlobals.queryArgs = foodyGlobals.queryArgs || {};

        this.searchFilter = {};

        for (let key in foodyGlobals.queryArgs) {
            if (foodyGlobals.queryArgs.hasOwnProperty(key)) {
                let type = foodyGlobals.queryArgs[key];
                this._updateFilterByQuery(type, this.locationUtils.getQuery(key));
            }
        }

        $checkboxes.each(function () {
            if (this.checked) {
                that.searchFilter[this.name] = that.getParsedInput(this);
            }
        });
    };

    FoodySearchFilter.prototype._updateFilterByQuery = function (type, valuesStr) {
        if (valuesStr) {
            valuesStr.split(',').forEach((value) => {
                let exclude = value.indexOf('-') !== -1;
                let valueNumeric = Math.abs(parseInt(value));
                let $checkbox = $(`input[type="checkbox"][data-exclude="${exclude}"][data-type="${type}"][data-value="${valueNumeric}"]`);
                $checkbox.prop('checked', true);
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
        let $checkedCheckboxes = $('input[type="checkbox"]:checked', pageContainer);

        let types = _.groupBy($checkedCheckboxes.toArray(), function (el) {
            return $(el).data('type');
        });

        let urlParams = new URLSearchParams();

        for (let key in types) {
            if (types.hasOwnProperty(key)) {

                let queryArg = _.invert(foodyGlobals.queryArgs)[key];

                let filterItems = types[key].map((el) => {
                    let data = $(el).data();
                    let {exclude, value} = data;

                    if (exclude) {
                        value *= -1;
                    }
                    return value;
                });

                urlParams.set(queryArg, filterItems.join(','));
            }
        }

        this.locationUtils.updateHistory(null, urlParams.toString())

    };

    return FoodySearchFilter;

})();