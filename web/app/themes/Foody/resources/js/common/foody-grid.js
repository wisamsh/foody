/**
 * Created by moveosoftware on 10/7/18.
 */

let FoodyLoader = require('./foody-loader');
module.exports = (function () {

    function FoodyGrid(settings) {
        this.init(settings);
    }

    FoodyGrid.prototype.init = function (settings) {
        this.$grid = $(settings.selector);
        // this.$grid.css('transition', 'opacity .3s');
        this.$parent = this.$grid.parent();
        this.$parent.css('position', 'relative');
        this.foodyLoader = new FoodyLoader({container: this.$parent});
        this.$loadMore = $('.show-more', this.$parent);

        let gridTitleSelector = settings.titleSelector;
        if (!gridTitleSelector) {
            this.$title = $('.grid-header .title', this.$parent);
        } else {
            this.$title = $(gridTitleSelector);
        }
    };

    FoodyGrid.prototype.getItems = function () {

        return $('.grid-item', this.$grid).map(function () {
            return $(this).data('id');
        }).get();

    };


    FoodyGrid.prototype.loading = function () {
        this.$grid.css('opacity', '0.3');
        this.foodyLoader.attach();
        this.$loadMore.addClass('disabled');
    };


    FoodyGrid.prototype.stopLoading = function () {
        this.$grid.css('opacity', '1');
        this.foodyLoader.detach();
        this.$loadMore.removeClass('disabled');
    };


    FoodyGrid.prototype.refresh = function (data, revertTitle) {
        this.$grid.empty();
        this.$grid.append(data.items);
        if (data.count < foodyGlobals.postsPerPage) {
            this.$loadMore.hide();
        } else {
            this.$loadMore.show();
        }

        this.updatePostsFound(data.found, revertTitle);
    };

    FoodyGrid.prototype.append = function (data) {
        let items = data.items;
        this.$grid.append(items);
        if (!data.next) {
            this.$loadMore.hide();
        } else {
            this.$loadMore.show();
        }

        // this.updatePostsFound(data.found);
    };

    FoodyGrid.prototype.updatePostsFound = function (found, revertTitle) {

        let title = this.$title.text();

        title = title.replace(/\([0-9]+\)/, '');

        if (!revertTitle) {
            title = `${title} (${found})`;
        }

        this.$title.text(title);

    };

    FoodyGrid.prototype.onLoadMore = function (callback) {
        this.$loadMore.click(callback);
    };


    return FoodyGrid;

})();

