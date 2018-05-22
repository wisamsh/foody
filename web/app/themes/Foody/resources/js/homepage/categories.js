/**
 * Created by moveosoftware on 5/20/18.
 */

$('.category-listing img');

let $target = $(".category-listing:last-child");
let $image = $('img', $target);

let imageHeight = $image.height();

$("<h4 class='overlay'> 25+</h4>").css({
    top: 0,
    'line-height': imageHeight + 'px'
}).appendTo($target.css("position", "relative"));

$image.css('opacity', '0.5');