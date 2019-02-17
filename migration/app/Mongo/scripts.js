/**
 * Created by moveosoftware on 8/1/18.
 */


db.recipemodels.aggregate(
    [{
        $project: {
            _id: 1,
            'RecipeIngredients.Ingredients': 1
        }
    },
        {
            $unwind: "$RecipeIngredients"
        },
        {
            $unwind: "$RecipeIngredients.Ingredients"
        },
        {
            $project: {
                name: '$RecipeIngredients.Ingredients.Ingredient',
                id:"$_id",
                _id:0
            }
        },
        {
            $out: "ing"
        },
    ]);

db.ing.aggregate([{$group: {_id: '$name', count: {$sum: 1}}},{$project:{name:'$_id',_id:0}}, {$out: 'ingredients'}]);

var allIngredients = db.ingredients.find().limit(10000);


db.recipemodels.find().forEach(function(recipe){

    recipe.RecipeIngredients.forEach(function (ri) {

        ri.Ingredients.forEach(function (r) {

            allIngredients.forEach(function (ing) {
                if(ing.name == r.Ingredient){
                    r._id = ing._id;
                }
            });
        });
    });

    db.recipemodels.update({_id:recipe._id},recipe);
});




db.recipemodels.find().forEach(function(recipe){

    recipe.RecipeIngredients.forEach(function (ri) {

        ri.Ingredients.forEach(function (r) {

            if(r.AmountTypes == 'ק'){
                r.AmountTypes = 'גרם';
                r.Amount = r.Amount || 1;
                r.Amount = r.Amount * 1000;
            }
        });
    });

    db.recipemodels.update({_id:recipe._id},recipe);
});

