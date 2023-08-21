/**
* Initially based on Omeka S omeka2importer.js and resource-core.js.
*/


    (function ($) {
        $(document).ready(function () {
            const itemsCheckbox = $("input[type='checkbox'][name='resource_ids[]']");
            const exportButton = $('#exportcsvbutton');
            const selectAll= exportButton.closest('.select-all');
            
            function updateExportButton() {
                if (itemsCheckbox.filter(':checked').length === 0) {
                    exportButton.text('Total Export');
                } else
                exportButton.text('Export Selection')
            }
    
            itemsCheckbox.on("click", updateExportButton);
            updateExportButton();
    
        });
    })(jQuery);
    