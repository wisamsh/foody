<style>

@media (min-width:1024px) {
   .tooltip-inner {
      max-width: none;
      white-space: nowrap;
   }
}

@media (min-width:600px) and (max-width:761px) {
   .tooltip-inner {
      max-width: none;
      white-space: nowrap;
   }
}

.topstats li {
   border: 1px solid #eeeeee;
   padding: 15px;
   margin: 0px 0px !important;
}

.topstats  li h3 {
   color:#674508
}

.topstats  li span i {
   color:green !important
}

.topstats {
   padding:0 0px !important
}

@media (min-width:660px) and (max-width:1024px) {
   .topstats li {
      border-right: 0px !important;
   }
}

</style>

<div class="container-widget">
<?php include_once('header.php');?>

    <div class="kode-alert kode-alert-icon alert6-light" style="height: 240px;">
        <p style="text-align:center"><i class="fa fa-info" style="position: unset; display: inline-block;" ></i> <?php if(!empty($pe_session['check_auth']['message'])) echo $pe_session['check_auth']['message'];?></p>
            <div style="text-align:center;margin-top: 80px;">
               <a href="https://app.pushengage.com/account/billing-subscription?drawer=true" target="_blank" ><button class="btn btn-primary btn-responsive" >Upgrade Your Plan</button></a>
            </div>
    </div>
</div>

