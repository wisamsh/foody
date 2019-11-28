module.exports = (function () {


    let TagManager = function () {
    };

    TagManager.prototype.pushDataLayer = function (
        category = '', action = '', label = '', customerID = '',
        recipe_name = '', item_category = '', chef = '',
        difficulty_level = '', preparation_time, ingredients_amount = '',
        order_location = '', amount = '', has_rich_content,
        cd_description1 = '', cd_value1 = '', filters_amount = '', _object = ''
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
                object = _object;
                break;
            case 'home':
                object = _object;
                break;
            case 'article':
                object = _object;
                break;
            case 'categories':
                object = _object;
                break;
            case 'category':
                object = _object;
                break;
            case 'author':
                object = _object;
                break;
            case 'channel':
                object = _object;
                break;
            case 'course':
                if (_object == '') {
                    object = decodeURI(window.location.pathname.replace('/courses/', '').replace(/-/g, ' '));
                    object = (object.length < 150) ? object : object.slice(0, 150);
                } else {
                    object = _object;
                }
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

