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

        let $sort = $(sort);

        let that = this;
        $sort.on('changed.bs.select', function () {
            let val = $(this).val();
            if (val) {
                that.sort(val);
            } else {
                console.log('no val')
            }
        });

    };

    PageContentPaging.prototype.sort = function (sort) {

        let ajaxSettings = this._prepareQuery(sort);

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


        let ajaxSettings = this._prepareQuery();

        this.filter.loading();
        let that = this;

        foodyAjax(ajaxSettings, function (err, data) {
            if (err) {
                // TODO handle
                return console.log(err);
            }
            that.filter.stopLoading();
            that.grid.append(data.data);
            that.updateQuery(page);
        });
    };


    PageContentPaging.prototype._prepareQuery = function (sort) {
        let page = this.getPageFromSearch();

        page++;
        let ajaxSettings = {
            action: 'load_more',
            data: {
                context: this.settings.context,
                page: page,
                filter: this.filter.prepareFilterForQuery(),
                context_args: this.settings.contextArgs,
                cols: this.filter.cols
            }
        };

        if (sort) {
            ajaxSettings.data.sort = sort;
            ajaxSettings.data.page--;
        }

        return ajaxSettings;

    };

    PageContentPaging.prototype.updateQuery = function (page) {
        if (history.pushState) {

            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + this.pageQuery + '=' + page;
            if (window.location.pathname == '/' || this.pathRegex.test(window.location.pathname)) {
                newurl = window.location.protocol + "//" + window.location.host + '/' + this.pageQuery + '/' + page;
            }
            window.history.pushState({path: newurl}, '', newurl);
        }
    };

    PageContentPaging.prototype.getPageFromSearch = function () {

        let urlParams = new URLSearchParams(window.location.search);
        let page = urlParams.get(this.pageQuery);
        if (!page) {
            let path = window.location.pathname;
            if (path == '/' || this.pathRegex.test(path)) {


                let matches = path.match(this.pathRegex);
                if (matches && matches.length) {
                    page = matches[1];
                    page = parseInt(page);
                }

                if (!page || isNaN(page)) {
                    page = 1;
                }
            }
        }

        if (!page) {
            page = 1;
        }

        return page;
    };

    PageContentPaging.prototype.log = function (logStr) {
        console.log(this.TAG, logStr);
    };

    return PageContentPaging;


})();