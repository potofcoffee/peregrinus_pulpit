(function ($) {

    $(document).ready(function () {
        $('.locationRelationField').select2({
            sorter: function (data) {
                /* Sort data using lowercase comparison */
                return data.sort(function (a, b) {
                    a = a.text.toLowerCase();
                    b = b.text.toLowerCase();
                    if (a > b) {
                        return 1;
                    } else if (a < b) {
                        return -1;
                    }
                    return 0;
                });
            }
        });
    });

})(jQuery);