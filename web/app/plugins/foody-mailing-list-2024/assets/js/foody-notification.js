jQuery(document).ready(function () {

    var newDiv = document.createElement('div');

    // Set attributes, styles, or other properties if needed
    newDiv.id = 'notification_Block';
    newDiv.className = 'notification_Block';
    newDiv.innerHTML = '<h3>שלחו לי התראה</h3>';
    var newButton = document.createElement('button');
    newButton.textContent = 'Click me';

    // Set onclick event handler
    newButton.onclick = function () {
        alert('Button clicked!');
        // Add your custom function here
    };


    $('#recipe-ingredients').before(newDiv);
    var divElement = document.getElementById('notification_Block');
    divElement.appendChild(newButton);

});
