<?php 
$MainPageContent = new MainPageContent ;
$get_MainBanner = $MainPageContent->get_MainBanner();
?>
<section>
    <div class="TopBannerMP"><img src="<?php echo $get_MainBanner ;?>" loading="lazy" class=""/></div>
<?php print_r($MainPageContent->Get_Promoted_Recipies());?>
</section>
