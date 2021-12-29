//eventCallback('', 'מתחם פידים', 'טעינה', channelName);

console.log(foodyGlobals.title);
//jQuery( document ).ready(function() {
    const tagers = {
     page:"תשובות", 
     action:"טעינת עמוד",
     category:'תשובה',
     action_lablel:'טעינה',
     label:'קטגוריה ראשית',
     cd_description1:'foody',
     cd_value1:'foody'
    };
    dataLayer.push({event:'foody', tagers});
//});