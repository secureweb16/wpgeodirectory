jQuery(function($) {
    $('#gd_ie_download_events').click(function(e) {
        if ($(this).data('sample-csv')) {
            window.location.href = $(this).data('sample-csv');
            return false;
        }
    });
});