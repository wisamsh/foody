/**
 * Created by moveosoftware on 11/5/18.
 */
module.exports = function (lock, overlay) {
    let lockClasses = 'lock';
    if(overlay){
        lockClasses = `${lockClasses} side-active`;
    }

    if(lock){
        jQuery('html,body').addClass(lockClasses);
    }else{
        jQuery('html,body').removeClass(lockClasses);
    }
};