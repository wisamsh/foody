/**
 * Created by moveosoftware on 10/25/18.
 */
let lastKnownScrollY = 0;
let currentScrollY = 0;
let ticking = false;
let idOfHeader = 'masthead';
let eleHeader = null;
const classes = {
    pinned: 'header-pin',
    unpinned: 'header-unpin',
};


function onScroll() {
    currentScrollY = window.pageYOffset;
    if (currentScrollY < lastKnownScrollY) {
        pin();
    } else if (currentScrollY > $(eleHeader).height()) {
        unpin();
    }
    lastKnownScrollY = currentScrollY;
}
function pin() {
    if (eleHeader.classList.contains(classes.unpinned)) {
        eleHeader.classList.remove(classes.unpinned);
        eleHeader.classList.add(classes.pinned);
    }
}
function unpin() {
    if (eleHeader.classList.contains(classes.pinned) || !eleHeader.classList.contains(classes.unpinned)) {
        eleHeader.classList.remove(classes.pinned);
        eleHeader.classList.add(classes.unpinned);
    }
}

if (foodyGlobals.isMobile) {

    window.onload = function () {
        eleHeader = document.getElementById(idOfHeader);
        document.addEventListener('scroll', onScroll, false);
    };
}
