jQuery(document).ready(function ($) {

    $('#composer-update-notice .spinner').addClass('is-active');
    $('input#submit').prop('disabled', true);
    $('a').click(function (e) {
        e.preventDefault();
    });

    var data = {
        'action': 'pulpit_composerUpdate'
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function (response) {
        response = JSON.parse(response);
        jQuery('#composer-update-notice .spinner').removeClass('is-active');
        jQuery('#composer-update-notice').html('<div class="spinner"></div><p>' + response.notice + '</p>');
        if (response.success) {
            jQuery('#composer-update-notice').removeClass('notice-warning').addClass('notice-success');
        } else {
            jQuery('#composer-update-notice').removeClass('notice-info').addClass('notice-error');
        }
        jQuery('input#submit').prop('disabled', false);
        jQuery('a').click(function () {
        });
    });
});