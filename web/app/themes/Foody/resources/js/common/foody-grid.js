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
        this.$grid.css('transition', 'opacity .3s');
        this.$parent = this.$grid.parent();
        this.$parent.css('position', 'relative');
        this.foodyLoader = new FoodyLoader({container: this.$parent});
    };

    FoodyGrid.prototype.getItems = function () {

        return $('.grid-item', this.$grid).map(function () {
            return $(this).data('id');
        }).get();

    };


    FoodyGrid.prototype.loading = function () {
        this.$grid.css('opacity', '0.3');
        this.foodyLoader.attach();
    };


    FoodyGrid.prototype.stopLoading = function () {
        this.$grid.css('opacity', '1');
        this.foodyLoader.detach();
    };


    FoodyGrid.prototype.refresh = function (items) {
        this.$grid.empty();
        this.$grid.append(items);
    };


    return FoodyGrid;

})();

