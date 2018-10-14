/**
 * Created by moveosoftware on 10/8/18.
 */
let FoodySearchFilter = require('../common/foody-search-filter');

jQuery(document).ready(($) => {
    new FoodySearchFilter({selector: '.foody_channel-template #accordion-foody-filter',grid: '#channel-recipe-feed', cols: 3});
    new FoodySearchFilter({selector: '.foody_channel-template #accordion-foody-filter',grid: '#channel-playlist-feed', cols: 3});
});