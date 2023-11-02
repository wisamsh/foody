(function () {
    tinymce.PluginManager.add('foody_mce_button', function (editor, url) {
        editor.addButton('foody_mce_button', {
            text: 'הוסף ריווח',
            icon: false,
            onclick: function () {
                var selectedElement = tinymce.activeEditor.selection.getNode();

                if (selectedElement) {
                    var spacingClass = 'foody-list-item-spacing';
                    selectedElement.classList.toggle(spacingClass);
                }

            }
        });
    });
})();