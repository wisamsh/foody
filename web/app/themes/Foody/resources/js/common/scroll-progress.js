/**
 * Created by moveosoftware on 6/27/18.
 */

module.exports = function () {


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