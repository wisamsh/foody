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
                let that = this;
                $(settings.searchButton).on('click', function () {
                    that.doQuery.call(that);
                });
            }
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

            console.log('check change: ', that.settings);


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

    FoodySearchFilter.prototype.prepareFilterForQuery = function (search) {

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


        search = search || '';

        let args = {
            // TODO get from input if needed
            search: search,
            types: [],
            context: this.settings.context,
            context_args: this.settings.contextArgs,
        };
        // TODO remove once unnecessary
        // args.context = _.uniq(this.initialContext.concat(this.grid.getItems()));


        //noinspection JSPotentiallyInvalidUsageOfThis
        for (let key in this.searchFilter) {
            if (this.searchFilter.hasOwnProperty(key)) {
                args.types.push(this.searchFilter[key]);
            }
        }


        // TODO remove once unnecessary
        // if (!args.search && args.types.length == 0) {
        //     args.context = this.initialContext;
        // }

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
        that.loading();
        foodyAjax(ajaxSettings, function (err, data) {
            that.stopLoading();
            if (err) {
                console.log('err: ' + err);
            } else {
                that.grid.refresh(data.data,ajaxSettings.data.data.types.length == 0);
            }
        });

    };


    return FoodySearchFilter;

})();