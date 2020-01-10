

(function($){

    function enableAgendaItemsFieldButtons() {
        $('.pulpit-agenda-items-form-single-toggle').click(function(){
            $(this).parent().find('.pulpit-agenda-items-form-sub').toggle();
            if ($(this).hasClass('ui-icon-arrowthick-1-s')) {
                $(this).removeClass('ui-icon-arrowthick-1-s').addClass('ui-icon-arrowthick-1-n');
            } else {
                $(this).removeClass('ui-icon-arrowthick-1-n').addClass('ui-icon-arrowthick-1-s');
            }
        });

        $('.pulpit-agenda-items-field-btn-remove').click(function(event){
            event.preventDefault();
            $(this).parent().remove();
        });
    }



    $(document).ready(function(){

        $('.pulpit-agenda-items-form').sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                // reorder indices
                var index = 0;
                $(this).find('.pulpit-agenda-items-form-single').each(function(){
                    $(this).find('input,select,textarea').each(function(){
                        $(this).attr('name', $(this).attr('name').replace(/(\d+)/g, index));
                        $(this).attr('id', $(this).attr('id').replace(/(\d+)/g, index));
                    });
                    $(this).find('label').each(function() {
                        $(this).attr('for', $(this).attr('for').replace(/(\d+)/g, index));
                    });
                    index++;
                });
            }
        }).disableSelection();

        $('.pulpit-agenda-items-field-btn-add').click(function(event){
            event.preventDefault();

            var index = $(this).parent().find('.pulpit-agenda-items-form-single').length;
            var code = agendaItemsFormEmptyRecord[$(this).parent().data('key')];
            code = code.replace(/###INDEX###/g, index);

            $(this).before($(code));
            enableAgendaItemsFieldButtons()
        });

        $('.pulpit-agenda-items-form-sub').toggle();
        $('.pulpit-agenda-items-form-single-toggle').removeClass('ui-icon-arrowthick-1-n').addClass('ui-icon-arrowthick-1-s');
        enableAgendaItemsFieldButtons();
    });
})(jQuery);