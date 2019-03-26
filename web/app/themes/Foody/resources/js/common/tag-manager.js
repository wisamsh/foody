module.exports = (function () {


    let TagManager = function () {
    };

    TagManager.prototype.pushDataLayer = function (
        category = '', action = '', label = '', customerID = '',
        recipe_name = '', item_category = '', chef = '',
        difficulty_level = '', preparation_time, ingredients_amount = '',
        order_location = '', amount = '', object, time, has_rich_content = '',
        cd_description1 = '', cd_value1 = '', filters_amount = ''
    ) {

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
            time,
            has_rich_content,
            cd_description1,
            cd_value1,
            filters_amount
        };

        window.dataLayer.push(dataLayerObj);
    };

    return TagManager;
})();

