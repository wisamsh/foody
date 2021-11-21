/**
 * Created by moveosoftware on 10/14/18.
 */

module.exports = (function () {

    let FoodyAnalytics = function (settings) {
        this.mixpanel = require('mixpanel-browser');
        this.mixpanel.init(foodyGlobals.mixpanelToken);
    };


    FoodyAnalytics.prototype.event = function (name, properties) {

        // TODO maybe add here more analytics tools
        this.mixpanel.track(name, properties);
    };

    FoodyAnalytics.prototype.timeEvent = function (name) {

        // TODO maybe add here more analytics tools
        this.mixpanel.time_event(name);
    };

    FoodyAnalytics.prototype.view = function () {

        let event = {
            id: foodyGlobals.objectID,
            title: foodyGlobals.title,
            type: foodyGlobals.type
        };

        this.event('page_view', event);

    };


    return FoodyAnalytics;


})();

//wisam

if(foodyGlobals.loggedInUser){
    let ebook_str = localStorage.getItem('ebook');
    console.log(ebook_str);
    //console.log("user" , foodyGlobals.loggedInUser);
    if(ebook_str && ebook_str == 'regular_regist'){
        let rishum = 'רישום באמצעות האתר הצליח - טעינת עמוד השלמת רישום';
        let cat_rish = 'רישם לאתר';
        let action_resh='רישום הצליח';
        let label_resh='אתר';
        let descr_rish='רישום דיוור';
        let cdval_rish='נרשם';
        let uid_rish = foodyGlobals.loggedInUser ;
        eventCallback(rishum, cat_rish, action_resh,label_resh,descr_rish,cdval_rish,uid_rish);
        //localStorage.setItem('ebook','');
        localStorage.removeItem('ebook')
        localStorage.clear();
   }
}








jQuery(document).ready(($) => {
   

    let lastInputSearch = undefined;

    /**
     * Load pop-up
     */
    let modalPopup = $('#login-modal');
    modalPopup.on('shown.bs.modal', function (event) {
        eventCallback(event, 'רישום לאתר', 'טעינת פופאפ', 'אתר', '', '');
    });

    /**
     * Pressed register on pop-up
     */
    let registerBtnOnPopup = $('#login-modal .go-to-register');
    registerBtnOnPopup.on('click', function (event) {
        eventCallback(event, 'רישום לאתר', 'לחיצה על קישור לקוח חדש? הירשם', 'אתר', '', '');
    });

    /**
     * searching without autocomplete
     */
    $('.search-bar > .icon > img').on('click', function () {
        let searchValue = $('.foody-input')[0].value;
        set_search_order('searches_strings', searchValue);
        eventCallback('', 'חיפוש', 'חיפוש טקסט חופשי', searchValue, 'מספר חיפוש', get_search_order('searches_strings', searchValue), 'חיפוש חופשי');
    });


    /**
     * searching with autocomplete
     */
    $('.foody-dataset-1').on('click', '.foody-search-suggestions a', function () {
        let searchValue = $('.foody-input')[0].value;
        let choiceSuggestion = this.innerText;
        let amount = $('.foody-search-suggestions')[0].childNodes.length;
        set_search_order('searches_strings', searchValue);
        eventCallback('', 'חיפוש', 'בחירה בתוצאה מוצעת', choiceSuggestion, 'מספר חיפוש', get_search_order('searches_strings', choiceSuggestion),'מנגנון תוצאות', amount);
    });

    $('.search .search-autocomplete').keydown((e) => {
        //todo: chekc if 'aria-expanded' === 'true'
    });
});

function set_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        searches_comitted = [];
    }

    if (!searches_comitted.includes(key)) {
        searches_comitted.push(key);
    }

    sessionStorage.setItem(action, JSON.stringify(searches_comitted));
}

function get_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        return 0;
    }

    return jQuery.inArray(key, searches_comitted) + 1;
}

let registerButtonNewAct = jQuery('#register_btn_ebook');
if(registerButtonNewAct.length){
    //console.log(foodyGlobals);
    //console.log("lenth ok");
registerButtonNewAct.on('click', null , function(){
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const campaign_for_ebook = urlParams.get('wcamp');
    //console.log(campaign_for_ebook);
    //eventCallback('רישום דרך האתר', 'רישום לאתר', 'רישום הצליח', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'רישום לדיוור', 'נרשם פלוס ספר');
if(campaign_for_ebook){
    //console.log(campaign_for_ebook)
    localStorage.setItem('ebook', 'regular_regist');
}

});

}


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 * @param cdDesc
 * @param cdValue
 */
function eventCallback(event, category, action, label, cdDesc, cdValue, object = '', _amount = '') {

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        _amount,
        '',
        cdDesc,
        cdValue,
        '',
        object
    );
}
