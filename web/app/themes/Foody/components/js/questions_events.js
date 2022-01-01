//eventCallback('', 'מתחם פידים', 'טעינה', channelName);

//console.log(foodyGlobals);

let MainCategory = '';
let AllCategories = [];
let technicsARR = [];
let author = foodyGlobals.author_name;
let title = foodyGlobals.title;
let accessoriesARR = [];

let cats = jQuery('div.categories');
let technics = jQuery('div.technics');
let accessories = jQuery('div.accessories');
let tags = jQuery('div.tags');

//console.log('LENGHT:', cats.length);
if (cats.length == 1) {
    jQuery("div.categories >  .post-categories > li:first-child a").each(function (index) {
        MainCategory = jQuery(this).text();

    });
    jQuery("div.categories >  .post-categories > li a").each(function (index) {
        let res = jQuery(this).text()
        AllCategories.push(res);
    });
}

if (technics.length == 1) {
    
    jQuery("div.technics >  .post-categories > li a").each(function (index) {
        let tech = jQuery(this).text()
        technics.push(tech);
    });
}


if (accessories.length == 1) {
    
    jQuery("div.accessories >  .post-categories > li a").each(function (index) {
        let items = jQuery(this).text()
        accessoriesARR.push(items);
    });
}



let allaccessories = Object.assign({}, accessoriesARR);

let alltechnics = Object.assign({}, technics);

let AllCatOBJ = Object.assign({}, AllCategories);

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

let PageLoad_AllCategory = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריות נוספות',
    cd_description1: 'מפרסם',
    cd_value1: author,
    object: '',
    amount: '',
    order_location: '',
    item_category: AllCatOBJ,
    chef: author,
    ingredient: '',
    recipe_name: title,
    has_rich_content: 1,
};
dataLayer.push({ event: 'foody', ...PageLoad_AllCategory });


let PageLoad_Technics = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריות משניות  אביזרים  טכניקות',
    cd_description1: 'מפרסם',
    cd_value1: author,
    object: '',
    amount: '',
    order_location: '',
    item_category: AllCatOBJ,
    object:alltechnics,
    chef: author,
    ingredient: '',
    recipe_name: title,
    has_rich_content: 1,
};
dataLayer.push({ event: 'foody', ...PageLoad_Technics });


let PageLoad_Accessorries = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריות משניות  אביזרים  טכניקות',
    cd_description1: 'מפרסם',
    cd_value1: author,
    object: '',
    amount: '',
    order_location: '',
    item_category: AllCatOBJ,
    object:allaccessories,
    chef: author,
    ingredient: '',
    recipe_name: title,
    has_rich_content: 1,
};
dataLayer.push({ event: 'foody', ...PageLoad_Accessorries });
