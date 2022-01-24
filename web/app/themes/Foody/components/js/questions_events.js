//eventCallback('', 'מתחם פידים', 'טעינה', channelName);

//console.log(foodyGlobals);
//console.log(FAQ_Details);
//console.log(dataLayer);






setInterval(myUserTiming, 1);
function myUserTiming() {
    let timing = 1;
    let timer = jQuery('#user_holdon').val();
    let fogo = ((Number(timer) + Number(timing) / 100 / 60 ));

    jQuery('#user_holdon').val(fogo);
}


let author = foodyGlobals.author_name;
let title = foodyGlobals.title;

const MainCategory = FAQ_Details.mainCategories;
const AllCategories = FAQ_Details.subcategories;
const technics = FAQ_Details.technics;
const accessories = FAQ_Details.accessories;

//FOR THE SEARCH FUCKUPS:================
jQuery(".icon img").click(function () {
let search = jQuery(".search").val();


    location.href = "/?s=" + search;

});



//location.href = "/?s=" + SearchFraz;
//=======================================

//console.log(AllCategories);
let PageLoad_MainCategory = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריה ראשית',
    cd_description1: 'מפרסם',
    cd_value1: author,
    object: '',
    amount: '',
    order_location: '',
    item_category: MainCategory,
    chef: author,
    ingredient: '',
    recipe_name: title,
    has_rich_content: 1,
};
dataLayer.push({ event: 'foody', ...PageLoad_MainCategory }); //the ...PageLoad_MainCategory means the object itema only not the object
PageLoad_MainCategory = {};

let PageLoad_AllCategory = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריות נוספות',
    cd_description1: 'מפרסם',
    cd_value1: author,
    object: '',
    amount: '',
    order_location: '',
    item_category: AllCategories,
    chef: author,
    ingredient: '',
    recipe_name: title,
    has_rich_content: 1,
};
dataLayer.push({ event: 'foody', ...PageLoad_AllCategory });
PageLoad_AllCategory = {};

// let PageLoad_Technics = {
//     category: 'תשובה',
//     action: 'טעינה',
//     label: 'קטגוריות משניות   טכניקות',
//     cd_description1: 'מפרסם',
//     cd_value1: author,
//     object: '',
//     amount: '',
//     order_location: '',
//     item_category: AllCategories,
//     object: technics,
//     chef: author,
//     ingredient: '',
//     recipe_name: title,
//     has_rich_content: 1,
// };
// dataLayer.push({ event: 'foody', ...PageLoad_Technics });

PageLoad_Technics = {}

// let PageLoad_Accessorries = {
//     category: 'תשובה',
//     action: 'טעינה',
//     label: 'קטגוריות משניות  אביזרים  ',
//     cd_description1: 'מפרסם',
//     cd_value1: author,
//     object: '',
//     amount: '',
//     order_location: '',
//     item_category: AllCategories,
//     object: accessories,
//     chef: author,
//     ingredient: '',
//     recipe_name: title,
//     has_rich_content: 1,
// };
// dataLayer.push({ event: 'foody', ...PageLoad_Accessorries });
// PageLoad_Accessorries = {};


//Clicking on related questions event : 
//===========================================================

jQuery(".related_question").click(function () {
    let RelatedTitle = (jQuery(this).text());


    let RelatedQuestionClick = {
        category: 'תשובה',
        action: 'מעבר לשאלה קשורה',
        label: (RelatedTitle),
        cd_description1: 'מפרסם',
        cd_value1: author,
        object: '',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        object: accessories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...RelatedQuestionClick });

});


//siderelated=================================================
jQuery(".siderelated a").click(function () {
    let RelatedRecipe = (jQuery(this).text());

    let RelatedRecipeClick = {
        category: 'תשובה',
        action: 'בחירת מתכון נוסף',
        label: (RelatedRecipe),
        cd_description1: 'מפרסם',
        cd_value1: author,
        object: 'מתכונים נוספים',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...RelatedRecipeClick });

});


//related_recepies_conduct========================================

jQuery(".feed-channel-details  .container .row .related_recepies_conduct a").click(function () {
    let RelatedRecipeCon = (jQuery(this).text());

    let RelatedRecipeConClick = {
        category: 'תשובה',
        action: 'בחירת מתכון נוסף',
        label: RelatedRecipeCon,
        cd_description1: 'מפרסם',
        cd_value1: author,
        object: 'פוטר',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...RelatedRecipeConClick });

});
jQuery("body").on("click", ".MobileConductor  .container .row .related_recepies_conduct a",function () {
//(".MobileConduct > .container .row .related_recepies_conduct a").click(function () {
  
    let RelatedRecipeConmMobile = (jQuery(this).text());

    let RelatedRecipeConMobileClick = {
        category: 'תשובה',
        action: 'בחירת מתכון נוסף',
        label: RelatedRecipeConmMobile,
        cd_description1: 'מפרסם',
        cd_value1: author,
        object: 'פוטר',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        object: accessories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...RelatedRecipeConMobileClick });

});


//categories Click ============================================================================

jQuery(".recipe-categories .categories  > .categories .post-categories li a").click(function () {
    let SelectedCategory = (jQuery(this).text());

    let SelectedCategoryClick = {
        category: 'תשובה',
        action: 'מעבר לקטגוריה',
        label: SelectedCategory,
        cd_description1: 'מיקום',
        cd_value1: 'פוטר',
        object: 'פוטר',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...SelectedCategoryClick });

});

//Before Leaving the page=================================================
// function BeforeUserLeave() {
//     // Write your business logic here
//     let timering = (jQuery('#user_holdon').val()) + ' M';
//     const tim = Math.round(timering);
//     let BeforeUserLeaving = {
//         category: 'תשובה',
//         action: 'טיימר',
//         label: tim,
//         cd_description1: 'מיקום',
//         cd_value1: 'פוטר',
//         object: 'פוטר',
//         amount: '',
//         order_location: '',
//         item_category: AllCategories,
//         object: accessories,
//         chef: author,
//         ingredient: '',
//         recipe_name: title,
//         has_rich_content: 1,
//     };
//     dataLayer.push({ event: 'foody', ...BeforeUserLeaving });
// }




jQuery(window).bind('beforeunload', function (e) {
   //console.log('Leaving you.....')
   // e.preventDefault(); 
    BeforeUserLeave();
    
    
});


//ON windowscroll (when user is scrolling up or down send precentage position ex: 47% scroll)
//============================================================================================
jQuery(window).on('scroll', jQuery.debounce(500,function () {
    let s = $(window).scrollTop(),
        d = $(document).height(),
        c = $(window).height();

    let scrollPercent = (s / (d - c)) * 100;
    let prestenge = 0;
if(scrollPercent > 1 && scrollPercent < 26){
    prestenge = 25;
}
if(scrollPercent > 25 && scrollPercent < 51){
    prestenge = 50;
}

if(scrollPercent > 52 && scrollPercent < 76){
    prestenge = 75;
}

if(scrollPercent > 76){
    prestenge = 100;
}

    let OnWindowScroll = {
        category: 'תשובה',
        action: 'גלילה',
        label: prestenge + '%',
        cd_description1: '',
        cd_value1: '',
        object: '',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...OnWindowScroll });
}));

//Search===================================================
jQuery("body").on("click", "#magnifier_search",function () {
    let SearchFraz = (jQuery(".search").val()); 
    let SearchDrazDoc = {
        category: 'תשובה',
        action: 'הפעלת חיפוש',
        label: SearchFraz,
        cd_description1: 'מפרסם',
        cd_value1: author,
        object: 'פוטר',
        amount: '',
        order_location: '',
        item_category: '',
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...SearchDrazDoc });
    //location.href = "/?s=" + SearchFraz;

});


//ON SOCIAL SHARE Click ===================================================
jQuery(".social-btn-container .icon-share").click(function () {
   

    let SocialShareClick = {
        category: 'תשובה',
        action: 'לחיצה לשיתוף',
        label: '',
        cd_description1: 'מפרסם',
        cd_value1: author,
        object: 'פוטר',
        amount: '',
        order_location: '',
        item_category: AllCategories,
        object: accessories,
        chef: author,
        ingredient: '',
        recipe_name: title,
        has_rich_content: 1,
    };
    dataLayer.push({ event: 'foody', ...SocialShareClick });
    

});