<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 11:52 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$nutritions = $template_args['nutritions'];
$title = $template_args['title'];

?>

<h2 class="title">
    <?php echo $title ?>
</h2>

<div class="nutrition-container">
    <div class="nutritions row">

        <?php foreach ($nutritions as $nutrition): ?>

            <div class="col-sm-4 col-12 nutrition">

                <?php foreach ($nutrition as $values): ?>
                    <div class="nutrition-row">
                    <span class="name">
                        <?php echo $values['name'] ?>
                    </span>

<!--                        <span class="value --><?php //echo $values['positive_negative'] ?><!--">-->
                        <span class="value">
                        <?php echo $values['value'] ?>
                    </span>
                    </div>
                    <div class="clearfix"></div>

                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>
    </div>
</div>

<!---->
<!--<div class="disclaimer">-->
<!---->
<!--    <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="bottom"-->
<!--    title="    --><?php //echo __('הערכים התזונתיים המופיעים במתכון חושבו על פי חומרי הגלם הספציפיים המופיעים וכל שינוי באופן הכנה, בסוג המוצר ו/או וחברת המצרך ו/או כמויות שונות יוביל לשינוי הערכים.
//אתר פודי וחברת מדיפוד בע"מ אינן אחראיות לכל שינוי כאמור ו/או על השימוש במתכון ובאחריות המשתמש לבדוק את ערכים התזונתיים בעצמו בטרם השימוש במתכון.')?><!--"-->
<!--    >-->
<!--    Tooltip on bottom-->
<!--    </button>-->
<!--</div>-->