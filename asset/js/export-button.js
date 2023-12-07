/**
* Initially based on Omeka S omeka2importer.js and resource-core.js.
*/


    (function ($) {
        $(document).ready(function () {
            const itemsCheckbox = $("input[type='checkbox'][name='resource_ids[]']");
            const exportButton = $('#exportcsvbutton');
            const selectAll= $("input[type='checkbox'].select-all");
            function updateExportButton() {
                if(selectAll.eq(0).prop("checked")){
                    exportButton.text('Export Page')
                }
                else if (itemsCheckbox.filter(':checked').length === 0) {
                    exportButton.text('Total Export');
                } else
                exportButton.text('Export Selection')
            }
    
            itemsCheckbox.on("click", updateExportButton);
            selectAll.on("click", updateExportButton);
            updateExportButton();
        });
    })(jQuery);
    