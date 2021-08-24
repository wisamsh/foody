<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/9/18
 * Time: 12:19 PM
 */

$page        = get_page_by_path( 'הרשמה' )->ID;
$image_right = get_field( 'right_image', $page );
$image_left  = get_field( 'left_image', $page );

$profile = $template_args['profile'];

$username  = $profile['username'];
$marketing = $template_args['marketing'];
$eBook     = $template_args['e-book'];

$campaign_link = get_field( 'campaign_link', $page );
?>

<section class="welcome">
    <div>
        <img src="<?php echo $GLOBALS['images_dir'] . 'avatar.png' ?>" alt="">

    </div>

    <h2>
		<?php echo sprintf( __( '%s, נעים להכיר!' ), $username ) ?>
        <br>
    </h2>
    <input class="marketing-approved" type="hidden" value="<?php echo $marketing ?>">
    <input class="e-book-approved" type="hidden" value="<?php echo $eBook ?>">

    <?php if($eBook !== "1"){ ?>
    <p class="no-ebook">
		<?php
		echo __( 'נרשמת בהצלחה, עכשיו נשאר לארגן את המטבח לארוחה הבאה.', 'foody' );
		?>
    </p>
    <?php } else { ?>
    <p class="with-ebook">
        <?php
        echo '<p style="text-align: center"><span style="font-size: 18pt">איזה כיף לכם שאתם חברים של פודי!</span></p>
<p style="text-align: center"><span style="font-size: 18pt">בתיבת המייל שלכם מחכה לכם החוברת המתכונים הדיגיטלית לחג!</span></p>';
        } ?>
    </p>


    <button class="btn btn-primary" aria-label="למעבר לעמוד הבית">

        <a href="<?php echo home_url() ?>"> <?php echo __( 'עבור לעמוד הראשי', 'foody' ) ?> </a>
    </button>

    <div class="campaign-button">
		<?php if ( ! empty( $campaign_link ) && ! empty( $campaign_link['title'] ) ): ?>
            <a class="btn" href="<?php echo $campaign_link['url'] ?>"
               target="<?php echo $campaign_link['target'] ?>">
				<?php echo $campaign_link['title'] ?>
            </a>
		<?php endif; ?>
    </div>

</section>

