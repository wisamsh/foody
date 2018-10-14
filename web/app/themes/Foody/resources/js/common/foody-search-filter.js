/**
 * Created by moveosoftware on 10/7/18.
 */

let FoodyGrid = require('./foody-grid');

module.exports = (function () {

    this.searchFilter = {};


    function FoodySearchFilter(settings) {
        this.init(settings);
    }

    FoodySearchFilter.prototype.init = function (settings) {
        this.settings = settings;
        this.grid = new FoodyGrid({selector: settings.grid});

        if (!settings.selector) {
            settings.selector = '#accordion-foody-filter';
        }

        this.cols = settings.cols;
        this.$filter = $(settings.selector);
        this.initialContext = this.grid.getItems();

        if (this.$filter.length) {

            //noinspection JSPotentiallyInvalidUsageOfThis
            this.searchFilter = this.buildInitialFilter();
            this.attachChangeListener();
            if (settings.searchButton) {
                $(settings.searchButton).click(this.doQuery);
            }
        }
    };

    FoodySearchFilter.prototype.attachChangeListener = function () {
        let $checkboxes = $('input[type="checkbox"]', this.$filter);
        let that = this;
        $checkboxes.change(function (e) {
            if (that.isLoading) {
                return;
            }
            e.preventDefault();
            that.loading();

            let data = $(this).data();
            let key = data.type + '_' + this.name;
            if (this.checked) {
                that.searchFilter[key] = that.getParsedInput(this);
            } else {
                if (that.searchFilter[key]) {
                    delete that.searchFilter[key];
                }
            }

            if (!foodyGlobals.isMobile) {
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
        let searchFilter = {};
        let that = this;
        $checkboxes.each(function () {
            if (this.checked) {
                that.searchFilter[this.name] = that.getParsedInput(this);
            }
        });

        return searchFilter;
    };

    FoodySearchFilter.prototype.getParsedInput = function (checkbox) {

        let data = $(checkbox).data();

        return {
            type: data.type,
            exclude: data.exclude,
            id: data.value
        };
    };

    FoodySearchFilter.prototype.prepareFilterForQuery = function () {

        /*
         * {
         *  search:'asfgag',
         *  posts ids to include in the filter
         *  context:[1,3,56],
         *  'types':[{
         *      type:'categories|ingredients|techniques|accessories|limitations|tags',
         *      exclude:false,
         *      id:8
         *  }]
         * }
         * */

        let args = {
            // TODO get from input if needed
            search: '',
            types: [],
            context: []
        };

        if (this.grid) {
            args.context = this.grid.getItems();
        }


        for (let key in this.searchFilter) {
            if (this.searchFilter.hasOwnProperty(key)) {
                args.types.push(this.searchFilter[key]);
            }
        }

        if (!args.search && args.types.length == 0) {
            args.context = this.initialContext;
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

        if (this.settings.onQuery && typeof  this.settings.onQuery == 'function') {
            this.settings.onQuery();
        }
        let that = this;
        foodyAjax(ajaxSettings, function (err, posts) {
            that.stopLoading();
            if (err) {
                console.log('err: ' + err);
            } else {
                that.grid.refresh(posts);
            }
        });

    };


    return FoodySearchFilter;

})();