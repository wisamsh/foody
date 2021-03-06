/**
 * Created by moveosoftware on 10/11/18.
 */

let FoodyLocationUtils = require('./foody-location-utils');

module.exports = (function () {

    let PageContentPaging = function (settings) {
        this.settings = settings;
        this.pageQuery = foodyGlobals.queryPage;
        this.TAG = 'PageContentPaging';
        this.pathRegex = /page\/([0-9]+(\/)?$)/;
        this.init();
        this.locationUtils = new FoodyLocationUtils();
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
            that.grid.refresh(data.data);
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
            that.locationUtils.updateHistory(ajaxSettings.data.page)
            let ref = that.locationUtils.getQuery('referer');
            if (ref && ref.length) {
                createRefererLinks(ref);
            } else if (foodyGlobals['referered_area']){
                createRefererLinks(foodyGlobals['referered_area']);
            }
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
                filter: this.filter.prepareFilterForQuery(this.locationUtils.getQuery('s')),
                context_args: this.settings.contextArgs,
                cols: this.filter.cols,
                ranged: ranged
            }
        };

        if (sort) {
            ajaxSettings.data.sort = sort;
        }

        var url = new URL(window.location.href);
        var idReferer = url.searchParams.get("referer");
        if(idReferer){
            ajaxSettings.data.referer = idReferer;
        } else if(foodyGlobals['referered_area'] && foodyGlobals['referered_area'].length) {
            ajaxSettings.data.referer = foodyGlobals['referered_area'];
        }


        return ajaxSettings;

    };

    PageContentPaging.prototype.updateQuery = function (currentPage) {
        if (history.pushState) {
            let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + this.pageQuery + '=' + currentPage;
            if (window.location.pathname === '/' || this.pathRegex.test(window.location.pathname)) {
                newUrl = window.location.protocol + "//" + window.location.host + '/' + this.pageQuery + '/' + currentPage;
                let urlParams = new URLSearchParams(window.location.search);
                newUrl = `${newUrl}?${urlParams.toString()}`;
            }

            window.history.pushState({path: newUrl}, '', newUrl);
        }
    };

    PageContentPaging.prototype.getPageFromSearch = function () {

        let currentPage = this.locationUtils.getQuery(this.pageQuery);
        if (!currentPage) {
            let path = window.location.pathname;
            if (path === '/' || this.pathRegex.test(path)) {
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