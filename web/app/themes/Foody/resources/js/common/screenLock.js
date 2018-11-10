/**
 * Created by moveosoftware on 11/5/18.
 */

const bodyScrollLock = require('body-scroll-lock');
const disableBodyScroll = bodyScrollLock.disableBodyScroll;
const enableBodyScroll = bodyScrollLock.enableBodyScroll;
const options = {

};

module.exports = function (lock,target, overlay) {
    let lockClasses = 'lock';
    if (overlay) {
        lockClasses = `${lockClasses} side-active`;
    }

    let targetElement = document.querySelector(target);

    if (lock) {
        disableBodyScroll(targetElement);
        jQuery('html,body').addClass(lockClasses);
    } else {
        enableBodyScroll(targetElement,options);
        jQuery('html,body').removeClass(lockClasses);
    }
};
