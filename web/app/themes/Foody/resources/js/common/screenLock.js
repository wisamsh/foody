/**
 * Created by moveosoftware on 11/5/18.
 */

const bodyScrollLock = require('body-scroll-lock');
const disableBodyScroll = bodyScrollLock.disableBodyScroll;
const enableBodyScroll = bodyScrollLock.enableBodyScroll;


module.exports = function (lock,target, overlay) {
    let lockClasses = '';
    if (overlay) {
        lockClasses = `${lockClasses} side-active`;
    }

    let targetElement = $(target)[0];

    if (lock) {
        jQuery('html,body').addClass(lockClasses);
        disableBodyScroll(targetElement);
    } else {
        jQuery('html,body').removeClass(lockClasses);
        enableBodyScroll(targetElement);
    }
};
