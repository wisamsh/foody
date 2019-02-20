/**
 * Created by moveosoftware on 10/11/18.
 */

module.exports = (function () {

    let FoodyLocationUtils = function () {
        this.pageQuery = foodyGlobals.queryPage;
        this.pathRegex = /page\/([0-9]+(\/)?$)/;
    };


    FoodyLocationUtils.prototype.updateHistory = function (page, search) {
        if (history.pushState) {


            let {protocol, pathname, host} = window.location;
            if (search == null) {
                search = window.location.search;
            }

            let url = `${protocol}//${host}`;
            let urlSearchParams = new URLSearchParams(search);

            if (this._isHome()) {
                if (page !== null) {
                    url = `${url}/${this.pageQuery}/${page}`
                } else {
                    url = `${url}${pathname}`;
                }
            } else {
                if (page !== null) {
                    urlSearchParams.set(this.pageQuery, page);
                }
                url = `${url}${pathname}`;
            }

            let urlSearchParamsStr = urlSearchParams.toString();
            if (urlSearchParamsStr && urlSearchParamsStr.trim()) {
                url = `${url}?${urlSearchParamsStr}`;
            }


            window.history.pushState({path: url}, '', url);
        }
    };

    FoodyLocationUtils.prototype._isHome = function () {
        return window.location.pathname === '/' || this.pathRegex.test(window.location.pathname);
    };

    FoodyLocationUtils.prototype.getQuery = function (key) {
        let urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(key);
    };

    return FoodyLocationUtils;

})();