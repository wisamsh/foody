/**
 * Created by moveosoftware on 6/27/18.
 */



module.exports = function () {
    // window.onscroll = function() {scroller()};
    //
    // function scroller() {
    //     let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    //     let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    //     let scrolled = (winScroll / height) * 100;
    //     document.getElementById("progress-bar").style.width = scrolled + "%";
    // }


    const win = $(window);
    const doc = $(document);
    const progressBar = $('progress');
    const setValue = () => win.scrollTop();
    const setMax = () => doc.height() - win.height();
    const setPercent = () => Math.round(win.scrollTop() / (doc.height() - win.height()) * 100);


    progressBar.attr({ value: setValue(), max: setMax() });

    doc.on('scroll', () => {

        progressBar.attr({ value: setValue() });
    });

    win.on('resize', () => {

        progressBar.attr({ value: setValue(), max: setMax() });
    })
};