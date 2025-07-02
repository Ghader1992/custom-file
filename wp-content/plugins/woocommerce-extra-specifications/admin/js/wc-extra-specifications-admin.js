jQuery(document).ready(function($) {
    var wrapper = $('#wc_extra_specifications_wrapper');
    if (!wrapper.length) {
        return;
    }

    var tableBody = wrapper.find('tbody');
    // Ensure rowIndex starts correctly even if there are no initial rows or only one.
    var rowIndex = tableBody.find('tr.wc-extra-spec-row').length;

    wrapper.on('click', '.wc-add-spec-row', function() {
        var newRowHtml = '<tr class="wc-extra-spec-row">' +
            '<td><input type="text" name="wc_extra_specifications[' + rowIndex + '][key]" value="" class="widefat" /></td>' +
            '<td><input type="text" name="wc_extra_specifications[' + rowIndex + '][value]" value="" class="widefat" /></td>' +
            '<td><button type="button" class="button wc-remove-spec-row">' + wc_extra_specs_admin.remove_text + '</button></td>' +
            '</tr>';
        tableBody.append(newRowHtml);
        rowIndex++; // Increment rowIndex after adding the row and using the current rowIndex for naming
    });

    wrapper.on('click', '.wc-remove-spec-row', function() {
        if (tableBody.find('tr.wc-extra-spec-row').length > 1) {
            $(this).closest('tr.wc-extra-spec-row').remove();
        } else {
            $(this).closest('tr.wc-extra-spec-row').find('input[type="text"]').val('');
            alert(wc_extra_specs_admin.last_row_alert);
        }
    });

    // Ensure at least one row is present on load, if the table body is empty.
    if (tableBody.find('tr.wc-extra-spec-row').length === 0) {
        var initialRowHtml = '<tr class="wc-extra-spec-row">' +
        '<td><input type="text" name="wc_extra_specifications[0][key]" value="" class="widefat" /></td>' +
        '<td><input type="text" name="wc_extra_specifications[0][value]" value="" class="widefat" /></td>' +
        '<td><button type="button" class="button wc-remove-spec-row">' + wc_extra_specs_admin.remove_text + '</button></td>' +
        '</tr>';
        tableBody.append(initialRowHtml);
        rowIndex = 1; // Set rowIndex to 1 as we've added the first row.
    }
});
