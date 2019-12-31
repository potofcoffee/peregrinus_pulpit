(function($) {
    if ($('#pulpit_pfarrplaner_dashboard').length) {
        $(document).ready(function () {
            $.getJSON('https://www.pfarrplaner.de/api/user/1/services', function (data) {
                var items = [];
                $.each( data['services'], function( key, val ) {
                    if (val['day']['name']) {
                        var title = 'Gottesdienst zum '+val['day']['name'];
                    } else {
                        var title = 'Gottesdienst';
                    }
                    var time = val['time'].substr(0,5);
                    var date = new Date(val['day']['date']);

                    var url = $('#pulpit_pfarrplaner_dashboard').data('url')+val['id'];

                    items.push( "<li id='pfarrplaner_service_item_" + key + "'>"
                        + date.getDate()+'.'+date.getMonth()+'.'+date.getFullYear()+ ', ' + time +' Uhr<br />'
                        +title
                        +'<a class="button button-small" href="'+url+'">Laden</a>'
                        + "</li>" );
                });
                $('#pulpit_pfarrplaner_dashboard').html(items.join(''));
            });
        });
    }
})(jQuery);