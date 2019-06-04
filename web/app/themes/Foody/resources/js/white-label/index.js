/**
 * Created by moveosoftware on 10/8/18.
 */
jQuery('.categories-block-content.categories-listing.show-more').each(function() {
    let block = this;
    showMoreList('#' + block.id + ' .col:last-child');
});
import('./brands');