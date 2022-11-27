
<?php 
$leftArrow = "https://foody-media.s3.eu-west-1.amazonaws.com/w_images/l-arrow.png";
$rightArrow = "https://foody-media.s3.eu-west-1.amazonaws.com/w_images/r-arrow.png";
?>
<div id="FoodyShopCarousel" class="carousel slide" data-ride="carousel" data-bs-ride="carousel" style="display:none;">
<div class="shoping_title"><?php echo $shop_block_title;?></div>
  <ol class="carousel-indicators"></ol>
  <div class="carousel-inner"></div>
  <a class="carousel-control-prev" href="#FoodyShopCarousel" role="button" data-slide="prev">
    <img class="car_arrow" src="<?php echo $leftArrow ;?>"/>
    <button id="prevbtn" role="button" data-slide="prev" style="display:none;"></button>


  </a>
  <a class="carousel-control-next" href="#FoodyShopCarousel" role="button" data-slide="next">
   <img class="car_arrow" src="<?php echo $rightArrow ;?>"/>
   <button id="nextbtn" role="button" data-slide="prev" style="display:none;"></button>
  </a>
</div>

<style>
.onsaleprice{
	text-decoration: line-through !important;
	
}
.shoping_title{
	width: 100%;
    text-align: center;
    font-size: 21px;
    color: #a6c1d4;
    font-weight: bold;
    margin-top: -20px;
    padding-top: 10px;
    padding-bottom: 10px;
    
}



.product_picker:hover{
	color:initial !important;
}
.product_picker:active{
	color:initial !important;
}

.product_picker:visited{
	color:initial !important;
}

.prod_price{
	font-size: 19px !important;
    font-weight: bold;
    margin-right: 5px;
    margin-left: 5px;
    line-height: 25px;
    color: #579fba;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 37px;
}
#FoodyShopCarousel{
	direction: rtl;
    width: 98%;
    background: #f7f7f7;
    -webkit-box-shadow: 0px 0px 5px 0px rgb(196 192 196);
    -moz-box-shadow: 0px 0px 5px 0px rgba(196,192,196,1);
    box-shadow: 0px 0px 5px 0px rgb(196 192 196);
    border-radius: 5px;
    text-align: center;
    justify-content: center;
    padding-bottom: 50px;
    margin: 0 auto;
}

.carousel-control-next, .carousel-control-prev{
	width:9% !important;
	margin: 5px
}

.carousel-control-next-icon, .carousel-control-prev-icon{
	
    width: 20px;
    height: 20px;
    background: #f44336 no-repeat 50%;
    background-size: 100% 100%;
    border-radius: 50%;
    padding: 20px;
}


.carousel-inner{
	text-align:center;
	
}

.product_holder img {
	width: 100%;
    object-fit: scale-down;
    max-height: 160px;
}

.buy_prod{
	width: 100%;
    background: #a6c1d4;
    color: #fff;
    height: 39px;
    text-align: center;
    padding-top: 6px;
    position: absolute;
    bottom: 0;
}

.shopping_cart {
	right: 11px;
    position: absolute;
    top: 12px;
    width: 17px
}

.product_holder {
	font-size: 16px;
    font-weight: bold;
    line-height: 28px;
	margin-top: 30px;
	background:#fff;
}





.product_wrapper {
	direction: rtl;
    width: 32%;
    height: 328px;
    background: linear-gradient( to top, #F7F7F7 50%, #fff 50%, #F7F7F7 50%, #fff 100% );
    -webkit-box-shadow: 0px 0px 5px 0px rgb(196 192 196);
    -moz-box-shadow: 0px 0px 5px 0px rgba(196,192,196,1);
    box-shadow: 0px 0px 5px 0px rgb(196 192 196);
    border: solid 1px #4444442e;
    position: relative;
    /* float: right; */
    margin-left: 1px;
    display: inline-table;
    -webkit-box-shadow: 0px 0px 5px 0px rgb(196 192 196);
    -moz-box-shadow: 0px 0px 5px 0px rgba(196,192,196,1);
    box-shadow: 0px 0px 5px 0px rgb(196 192 196);
    border: solid 1px #4444442e;
    position: relative;
    margin-right: 2px;
}


.prod_title{
	font-size: 14px;
    font-weight: bold;
    margin-right: 5px;
    margin-left: 5px;
    line-height: 18px;
    margin-top: 8px;
    height: 52px;
	
}

.prod_brand{
	
	font-size: 13px;
    font-weight: bold;
    margin-top: 8px;
	line-height: 35px;
}

.prod_second_title{
	display:none;
	font-size: 12px;
    margin-right: 5px;
    margin-left: 5px;
	
}

.prod_price{
	font-size: 14px;
    font-weight: bold;
    margin-right: 5px;
    margin-left: 5px;
    line-height: 25px;
	color:#579fba;
}
.onsale{
	background: #97b7cee8;
    position: absolute;
    top: 9px;
    left: 0;
    width: 77px;
    height: 22px;
    font-size: 14px;
    text-align: center;
    color: #fff;
	clip-path: polygon(100% 0, 91% 50%, 100% 100%, 0 100%, 0% 50%, 0 1%);
	
}

.carousel-indicators{margin-top:20px;}
.carousel-indicators .active {
    width: 12px;
    height: 12px;
    margin: 0;
    background-color: #a6c1d4;
	border:solid 1px #fff;
	 border-radius: 10px;
}

.carousel-indicators li{
	display: inline-block;
    width: 10px;
    height: 10px;
    margin: 1px;
    text-indent: -999px;
    cursor: pointer;
    background-color: #fff;
    border: 1px solid #999;
    border-radius: 10px;
}

.carousel-indicators {
    position: absolute;
    right: 0;
    bottom: -6px;
    left: 0;
    z-index: 15;
    display: flex;
    justify-content: center;
    padding:0px !important;
    margin-right: 15%;
    margin-left: 15%;
    list-style: none;
}

</style>
<script>
document.addEventListener('touchstart', handleTouchStart, false);        
document.addEventListener('touchmove', handleTouchMove, false);

var xDown = null;                                                        
var yDown = null;

function getTouches(evt) {
  return evt.touches ||             // browser API
         evt.originalEvent.touches; // jQuery
}                                                     
                                                                         
function handleTouchStart(evt) {
    const firstTouch = getTouches(evt)[0];                                      
    xDown = firstTouch.clientX;                                      
    yDown = firstTouch.clientY;                                      
};                                                
                                                                         
function handleTouchMove(evt) {
    if ( ! xDown || ! yDown ) {
        return;
    }

    var xUp = evt.touches[0].clientX;                                    
    var yUp = evt.touches[0].clientY;

    var xDiff = xDown - xUp;
    var yDiff = yDown - yUp;
                                                                         
    if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {/*most significant*/
        if ( xDiff > 0 ) {
            jQuery('#nextbtn').trigger('click');
        } else {
            jQuery('#prevbtn').trigger('click');
        }                       
    } else {
        if ( yDiff > 0 ) {
            /* down swipe */ 
        } else { 
            /* up swipe */
        }                                                                 
    }
    /* reset values */
    xDown = null;
    yDown = null;                                             
};

</script>


