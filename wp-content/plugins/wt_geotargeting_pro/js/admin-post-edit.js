jQuery(function($) {
    jQuery('#btn-check-all').on('click', function(e) {
        jQuery('#table1').checkboxes('check');
        e.preventDefault();
    });
});