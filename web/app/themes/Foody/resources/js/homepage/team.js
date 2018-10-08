/**
 * Created by moveosoftware on 5/15/18.
 */


jQuery(document).ready(($)=>{
    if (!foodyGlobals.isMobile) {
        showMoreList('.homepage .team-listing .author:last-child');
    } else {
        showSlider()
    }
});




function showSlider() {
    let teamSliderSelector = '.homepage .team-listing';
    $('.homepage .team-listing .author').removeClass('col');
    slider(teamSliderSelector, {
        slidesToShow: 1,
        rtl: true,
        variableWidth: true,
        arrows: false,
    });
}