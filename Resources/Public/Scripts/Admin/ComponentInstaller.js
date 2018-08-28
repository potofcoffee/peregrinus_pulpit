(function ($) {
    $(document).ready(function ($) {


        $('.pulpit-component-install-button').click(function () {
            var component = $(this).data('component');
            var installButton = $(this);
            //$(this).hide();
            var parent = $(this).parent();
            parent.find('.spinner').addClass('is-active');
            var data = {
                'action': 'pulpit_component_' + component
            };
            alert(data.action);
            jQuery.post(ajaxurl, data, function (response) {
                response = JSON.parse(response);
                if (response.notice) parent().lastChild().insertAfter(response.notice);
                if (response.success) {
                    parent.removeClass('component-box-warning').addClass('component-box-success');
                } else {
                    parent.removeClass('component-box-warning').addClass('component-box-error');
                    installButton.show();
                }
                parent.find('.spinner').removeClass('is-active');
            });
        });
    });

})(jQuery);