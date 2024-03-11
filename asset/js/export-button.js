/**
* Initially based on Omeka S omeka2importer.js and resource-core.js.
*/


(function ($) {
    $(document).ready(function () {
        const itemsCheckbox = $("input[type='checkbox'][name='resource_ids[]']");
        const exportSelectedButton = $('#export-selected-button');
        const selectAll = $("input[type='checkbox'].select-all");

        function updateExportSelectedButton() {
            if (itemsCheckbox.filter(':checked').length > 0) {
                $(exportSelectedButton).show();
            } else {
                $(exportSelectedButton).hide();
            }
        }

        itemsCheckbox.on("change", updateExportSelectedButton);
        selectAll.on("change", updateExportSelectedButton);
    });
})(jQuery);
