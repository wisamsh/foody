//eventCallback('', 'מתחם פידים', 'טעינה', channelName);

//console.log(foodyGlobals);
//console.log(FAQ_Details);
console.log(dataLayer);

let author = foodyGlobals.author_name;
let title = foodyGlobals.title;

const MainCategory = FAQ_Details.mainCategories;
const AllCategories = FAQ_Details.subcategories;
const technics = FAQ_Details.technics;
const accessories = FAQ_Details.accessories;


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
dataLayer.push({event: 'foody', ...PageLoad_MainCategory }); //the ...PageLoad_MainCategory means the object itema only not the object
PageLoad_MainCategory={};

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
PageLoad_AllCategory={};

let PageLoad_Technics = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריות משניות   טכניקות',
    cd_description1: 'מפרסם',
    cd_value1: author,
    object: '',
    amount: '',
    order_location: '',
    item_category: AllCategories,
    object: technics,
    chef: author,
    ingredient: '',
    recipe_name: title,
    has_rich_content: 1,
};
dataLayer.push({ event: 'foody', ...PageLoad_Technics });
PageLoad_Technics={}

let PageLoad_Accessorries = {
    category: 'תשובה',
    action: 'טעינה',
    label: 'קטגוריות משניות  אביזרים  ',
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
dataLayer.push({ event: 'foody', ...PageLoad_Accessorries });
PageLoad_Accessorries={};
//Clicking on related questions event : 
//===========================================================

jQuery( ".related_question" ).click(function() {
    let RelatedTitle = ( jQuery(this).text() );


let RelatedQuestionClick = {
    category: 'תשובה',
    action: 'מעבר לשאלה קשורה',
    label: RelatedTitle,
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

