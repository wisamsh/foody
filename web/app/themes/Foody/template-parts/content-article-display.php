<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/14/18
 * Time: 1:27 PM
 */


/** @var Foody_Article $foody_page */
$foody_page = Foody_PageContentFactory::get_instance()->get_page();
?>


<?php if ($foody_page->Get_Recipes_Title() !=""){?>
<section class="categories section no-print">
	<?php echo $foody_page->Get_Content_Before_Items() ;?>
    <h1 class="recipe_title">
		<b><?php echo( $foody_page->Get_Recipes_Title() ); ?></b>
    </h2>
	<?php 

	
	?>
	<!--<a href="https://wa.me/?photo=<?php echo $ttt;?>"/>whats</a>-->
	<?php 

	?>

</section>

<?php }?>

<?php 	echo $foody_page->Go_Recipes_For_Posts();?>

<?php 
echo $foody_page->Get_Schedual_Photos();
?>





<?php
the_content();
?>


<section class="categories section no-print">
    <h2 class="title">
		<?php echo __( 'קטגוריות' ) ?>
    </h2>
	<?php
	echo get_the_category_list( '', '' );
	?>

</section>

<?php
$tags = wp_get_post_tags( get_the_ID() );
if ( ! empty( $tags ) ) {
	?>
    <section class="tags section no-print">
        <h2 class="title">
			<?php echo __( 'תגיות', 'foody' ) ?>
        </h2>

		<?php

		foody_get_template_part( get_template_directory() . '/template-parts/content-tags.php', $tags );
		?>
    </section>
	<?php
}
?>

<section class="newsletter no-print">
	<?php $foody_page->newsletter(); ?>

</section>

<section class="comments section no-print">
	<?php
	if ( is_comments_open( $foody_page->id ) ) {
		$template = '';
		if ( wp_is_mobile() ) {
			$template = '/comments-mobile.php';
		}
		comments_template( $template );
	}
	?>
</section>
<?php if ( function_exists( 'footabc_add_code_to_content' ) ): ?>
    <section class="footab-container">
		<?php echo footabc_add_code_to_content(); ?>
    </section>
<?php endif; ?>
