/**
 * Created by moveosoftware on 10/11/18.
 */

module.exports = (function () {

    let PageContentPaging = function (settings) {
        this.settings = settings;
        this.pageQuery = foodyGlobals.queryPage;
        this.TAG = 'PageContentPaging';
        this.pathRegex = /page\/([1-9]+(\/)?$)/;
        this.init();
    };

    PageContentPaging.prototype.init = function () {

        this.filter = this.settings.filter;

        /**
         * FoodyGrid
         * */
        this.grid = this.filter.grid;
        let that = this;
        this.grid.onLoadMore(function () {
            that.loadMore();
        });

        if (this.settings.sort) {
            this.attachSort(this.settings.sort);
        }
    };

    PageContentPaging.prototype.attachSort = function (sort) {

        this.$sort = $(sort);

        let that = this;
        this.$sort.on('changed.bs.select', function () {
            let val = $(this).val();
            if (val) {
                that.sort(val);
            } else {
                console.log('no val')
            }
        });

    };

    PageContentPaging.prototype.sort = function (sort) {

        let ajaxSettings = this._prepareQuery(sort, false, true);

        this.filter.loading();
        let that = this;

        foodyAjax(ajaxSettings, function (err, data) {
            if (err) {
                // TODO handle
                return console.log(err);
            }
            that.filter.stopLoading();
            that.grid.refresh(data.data.items);
        });

    };


    PageContentPaging.prototype.loadMore = function () {

        let sort = '';
        if (this.settings.sort) {
            sort = this.$sort.val();
        }

        let ajaxSettings = this._prepareQuery(sort, true);

        this.filter.loading();
        let that = this;

        foodyAjax(ajaxSettings, function (err, data) {
            if (err) {
                // TODO handle
                return console.log(err);
            }
            that.filter.stopLoading();
            that.grid.append(data.data);
            that.updateQuery(ajaxSettings.data.page);
        });
    };

    /**
     * Creates the ajax request to use in paging and sorting
     * actions.
     * @param sort String current sorting method
     * @param loadMore Boolean should the query increment current page
     * @param ranged Boolean if true skip,offset will be used to fetch items until current page.
     * Otherwise will only fetch next page
     * @return ajax request object
     * */
    PageContentPaging.prototype._prepareQuery = function (sort, loadMore, ranged) {
        let page = this.getPageFromSearch();


        if (loadMore) {
            page++;
        }
        let ajaxSettings = {
            action: 'load_more',
            data: {
                context: this.settings.context,
                page: page,
                filter: this.filter.prepareFilterForQuery(),
                context_args: this.settings.contextArgs,
                cols: this.filter.cols,
                ranged: ranged
            }
        };

        if (sort) {
            ajaxSettings.data.sort = sort;
        }


        return ajaxSettings;

    };

    PageContentPaging.prototype.updateQuery = function (currentPage) {
        if (history.pushState) {
            console.log(this.pageQuery, currentPage);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + this.pageQuery + '=' + currentPage;
            if (window.location.pathname == '/' || this.pathRegex.test(window.location.pathname)) {
                newurl = window.location.protocol + "//" + window.location.host + '/' + this.pageQuery + '/' + currentPage;
            }
            window.history.pushState({path: newurl}, '', newurl);
        }
    };

    PageContentPaging.prototype.getPageFromSearch = function () {

        let urlParams = new URLSearchParams(window.location.search);
        let currentPage = urlParams.get(this.pageQuery);
        if (!currentPage) {
            let path = window.location.pathname;
            if (path == '/' || this.pathRegex.test(path)) {


                let matches = path.match(this.pathRegex);
                if (matches && matches.length) {
                    currentPage = matches[1];
                    currentPage = parseInt(currentPage);
                }

                if (!currentPage || isNaN(currentPage)) {
                    currentPage = 1;
                }
            }
        }

        if (!currentPage) {
            currentPage = 1;
        }

        return currentPage;
    };

    PageContentPaging.prototype.log = function (logStr) {
        console.log(this.TAG, logStr);
    };

    return PageContentPaging;


})();