<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 11:52 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
$nutritions   = $template_args['nutritions'];
$title        = $template_args['title'];
$dishes       = $template_args['dishes_amount'];
$dishes_title = $template_args['dishes_title'];

$disclaimer = __(
	"הערכים התזונתיים המופיעים במתכון חושבו על פי חומרי הגלם הספציפיים המופיעים וכל שינוי באופן הכנה, בסוג המוצר ו/או וחברת המצרך ו/או כמויות שונות יוביל לשינוי הערכים.
אתר פודי וחברת מדיפוד בע\"מ אינן אחראיות לכל שינוי כאמור ו/או על השימוש במתכון ובאחריות המשתמש לבדוק את ערכים התזונתיים בעצמו בטרם השימוש במתכון."
);

$disclaimer = sprintf( '<div> <span class="close">&times;</span><div>%s</div></div>', $disclaimer );


?>

<h2 class="title">
	<?php echo $title ?>

    <span class="glyphicon glyphicon-info-sign disclaimer" data-toggle="tooltip" data-placement="bottom"
          title='<?php echo $disclaimer ?>' data-content="<?php echo esc_html( $disclaimer ) ?>" data-html="true">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="22px"
                 height="22px" viewBox="0 0 22 22" version="1.1">
        <!-- Generator: Sketch 52.5 (67469) - http://www.bohemiancoding.com/sketch -->
        <title>מידע נוסף</title>
        <desc>מידע נוסף</desc>
        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g id="Playlist-Page" transform="translate(-739.000000, -3712.000000)" fill="#ED3D48" fill-rule="nonzero"
               stroke="#ED3D48" stroke-width="0.799999952">
                <g id="nutrition-copy" transform="translate(159.000000, 3707.000000)">
                    <g id="np_info_1983617_FFFFFF" transform="translate(581.000000, 6.000000)">
                        <path d="M10,20 C4.5,20 0,15.5 0,10 C0,4.5 4.5,0 10,0 C15.5,0 20,4.5 20,10 C20,15.5 15.5,20 10,20 Z M10,1.6664 C5.4164,1.6664 1.6664,5.4164 1.6664,10 C1.6664,14.5836 5.4164,18.3336 10,18.3336 C14.5836,18.3336 18.3336,14.5836 18.3336,10 C18.3336,5.4164 14.5836,1.6664 10,1.6664 Z M10,15.8328 C9.5,15.8328 9.1664,15.4992 9.1664,14.9992 L9.1664,9.1656 C9.1664,8.6656 9.5,8.332 10,8.332 C10.5,8.332 10.8336,8.6656 10.8336,9.1656 L10.8336,14.9992 C10.8336,15.4156 10.5,15.8328 10,15.8328 Z M10,4.7492 C10.5,4.7492 10.9164,5.1656 10.9164,5.6656 C10.9164,6.1656 10.5,6.582 10,6.582 C9.5,6.582 9.0836,6.1656 9.0836,5.6656 C9.0836,5.1656 9.5,4.7492 10,4.7492 Z"
                              id="Shape"/>
                    </g>
                </g>
            </g>
        </g>
    </svg>
    </span>


</h2>

<div class="nutrition-container">

    <div class="nutritions-header">
        <span class="clearfix"></span>
        <span>
            לפי <?php echo $dishes_title['singular'] ?>
        </span>
        <span>
            לפי
            <span class="nutrients-header-dishes-amount">
                <?php echo $dishes ?>
            </span>
            <?php echo $dishes_title['plural'] ?>
        </span>
    </div>

    <div class="nutritions row">
		<?php
		$count = 0;
		foreach ( $nutritions as $nutrition ): ?>

            <div class="col-12 nutrition">

				<?php foreach ( $nutrition as $item ): ?>
                    <div class="nutrition-row <?php echo( ++ $count % 2 ? "odd" : "even" ) ?>"
                         data-name="<?php echo $item['data_name'] ?>"
                         data-original=" <?php echo $item['value'] ?>">

                        <span class="name">
                            <?php echo $item['name'] ?>
                        </span>

                        <span class="dish-nutrition">
                            <span class="value">
                                <?php echo $item['valuePerDish'] ?>
                            </span>
                            <span class="unit">
                                <?php echo $item['unit'] ?>
                            </span>
                        </span>

                        <span class="chosen-dishes-nutrition">
                            <span class="value">
                                <?php echo $item['value'] ?>
                            </span>
                            <span class="unit">
                                <?php echo $item['unit'] ?>
                            </span>
                        </span>
                    </div>
                    <div class="clearfix"></div>

				<?php endforeach; ?>
            </div>

		<?php endforeach; ?>
    </div>
</div>