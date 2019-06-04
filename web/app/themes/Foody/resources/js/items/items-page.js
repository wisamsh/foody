let sorter = import('../common/sort');

jQuery(document).ready(($)=>{
   let sort  = sorter('.grid-body','.item');


    let sortVal = $('#items-sort').val();
    if(sortVal){
        sort(sortVal,'.grid-body','.item');
    }

});