module.exports = (function () {


    let TagManager = function () {
    };

    TagManager.prototype.pushDataLayer = function (
        category = '', action = '', label = '', customerID = '',
        recipe_name = '', item_category = '', chef = '',
        difficulty_level = '', preparation_time, ingredients_amount = '',
        order_location = '', amount = '', has_rich_content,
        cd_description1 = '', cd_value1 = '', filters_amount = ''
    ) {

        /**
         * Current time - formatted MM:SS
         */
        let d = new Date();
        let date = d.getMinutes() + ':' + d.getSeconds();

        /**
         * Object - Page type title
         */
        let object = '';
        switch (foodyGlobals['type']) {
            case 'recipe':
                object = '';
                break;
            case 'home':
                object = '';
                break;
            case 'article':
                object = '';
                break;
            case 'categories':
                object = '';
                break;
            case 'category':
                object = '';
                break;
            case 'author':
                object = '';
                break;
            case 'channel':
                object = '';
                break;
        }

        // Data Layer as an Object
        let dataLayerObj = {
            event: 'foody',
            category,
            action,
            label,
            customerID,
            recipe_name,
            'item-category': item_category,
            chef,
            difficulty_level,
            preparation_time,
            ingredients_amount,
            order_location,
            amount,
            object,
            date,
            has_rich_content,
            cd_description1,
            cd_value1,
            filters_amount
        };

        window.dataLayer.push(dataLayerObj);
    };

    return TagManager;
})();

