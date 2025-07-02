jQuery(document).ready(function($) {
    var wrapper = $('#wc_extra_specifications_wrapper');
    // If the metabox wrapper isn't on the page, do nothing.
    if (!wrapper.length) {
        return;
    }

    var tableBody = wrapper.find('tbody');
    var rowIndex; // Declare rowIndex here

    // Function to initialize or update rowIndex based on current rows
    function updateRowIndex() {
        rowIndex = tableBody.find('tr.wc-extra-spec-row').length;
    }

    // Initial rowIndex calculation
    updateRowIndex();

    // Ensure at least one row is present on load if the table body is empty.
    // This handles the case where PHP might not output any rows if there's no saved data.
    if (rowIndex === 0) {
        var initialKeyName = 'wc_extra_specifications[0][key]';
        var initialValueName = 'wc_extra_specifications[0][value]';
        var initialRowHtml = '<tr class="wc-extra-spec-row">' +
            '<td><input type="text" name="' + initialKeyName + '" value="" class="widefat" /></td>' +
            '<td><input type="text" name="' + initialValueName + '" value="" class="widefat" /></td>' +
            '<td><button type="button" class="button wc-remove-spec-row">' + wc_extra_specs_admin.remove_text + '</button></td>' +
            '</tr>';
        tableBody.append(initialRowHtml);
        updateRowIndex(); // Re-calculate rowIndex after adding the initial row
    }

    wrapper.on('click', '.wc-add-spec-row', function() {
        // rowIndex is already the count of existing rows, so it's the correct index for the new row's inputs.
        var newKeyName = 'wc_extra_specifications[' + rowIndex + '][key]';
        var newValueName = 'wc_extra_specifications[' + rowIndex + '][value]';

        var newRowHtml = '<tr class="wc-extra-spec-row">' +
            '<td><input type="text" name="' + newKeyName + '" value="" class="widefat" /></td>' +
            '<td><input type="text" name="' + newValueName + '" value="" class="widefat" /></td>' +
            '<td><button type="button" class="button wc-remove-spec-row">' + wc_extra_specs_admin.remove_text + '</button></td>' +
            '</tr>';
        tableBody.append(newRowHtml);
        updateRowIndex(); // Update rowIndex after adding a new row
    });

    wrapper.on('click', '.wc-remove-spec-row', function() {
        if (tableBody.find('tr.wc-extra-spec-row').length > 1) {
            $(this).closest('tr.wc-extra-spec-row').remove();
        } else {
            // If it's the last row, clear its fields instead of removing it.
            $(this).closest('tr.wc-extra-spec-row').find('input[type="text"]').val('');
            alert(wc_extra_specs_admin.last_row_alert);
        }
        // No need to updateRowIndex() here as it doesn't affect adding new rows with correct indices.
    });
});
