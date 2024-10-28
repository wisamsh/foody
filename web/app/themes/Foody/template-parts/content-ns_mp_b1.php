<?php 
$MainPageContent = new MainPageContent ;
$get_MainBanner = $MainPageContent->get_MainBanner();
?>
<div class="mp_main_container container">
<section id="topbanner">
    <div class="TopBannerMP"><img src="<?php echo $get_MainBanner ;?>" loading="lazy" class=""/></div>
</section>

<section id="front_recipies_desktop">
<?php  $args = $MainPageContent->Get_Promoted_Recipies();
echo $MainPageContent->fp_main_recipies($args);
?>
</section>

<section id="hotspots">
    <?php echo ($MainPageContent->mp_Hotspots());?>
</section>

</div>
