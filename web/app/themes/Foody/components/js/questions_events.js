//eventCallback('', 'מתחם פידים', 'טעינה', channelName);

console.log(foodyGlobals);

let MainCategory = '';
let AllCategories = [];
let author = foodyGlobals.author_name;
let title = foodyGlobals.title;


jQuery( "div.categories >  .post-categories > li:first-child a" ).each(function( index ) {
    MainCategory =  jQuery( this ).text() ;
    
  });
  jQuery( "div.categories >  .post-categories > li a" ).each(function( index ) {
    let rr = jQuery( this ).text()
    AllCategories.push(rr);
  });
  


console.log(AllCategories);
    let PageLoad_MainCategory = {
     category: 'תשובה', 
     action:'טעינה',
     label : 'קטגוריה ראשית',
     cd_description1 : 'מפרסם',
     cd_value1 : author,
     object:'',
     amount:'',
     order_location:'',
     item_category:MainCategory,
     chef:author,
     ingredient:'',
     recipe_name:title,
     has_rich_content:1,
    };
    dataLayer.push({event:'foody', PageLoad_MainCategory});

    let PageLoad_AllCategory = {
        category: 'תשובה', 
        action:'טעינה',
        label : 'קטגוריות נוספות',
        cd_description1 : 'מפרסם',
        cd_value1 : author,
        object:'',
        amount:'',
        order_location:'',
        item_category:AllCategories,
        chef:author,
        ingredient:'',
        recipe_name:title,
        has_rich_content:1,
       };
       dataLayer.push({event:'foody', PageLoad_AllCategory});


