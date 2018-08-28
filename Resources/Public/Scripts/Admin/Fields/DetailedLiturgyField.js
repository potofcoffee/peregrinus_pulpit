
function enableDetailedLiturgyFieldButtons() {
    $('.pulpit-detailed-liturgy-form-single-toggle').click(function(){
        $(this).parent().find('.pulpit-detailed-liturgy-form-sub').toggle();
        if ($(this).hasClass('ui-icon-arrowthick-1-s')) {
            $(this).removeClass('ui-icon-arrowthick-1-s').addClass('ui-icon-arrowthick-1-n');
        } else {
            $(this).removeClass('ui-icon-arrowthick-1-n').addClass('ui-icon-arrowthick-1-s');
        }
    });

    $('.pulpit-detailed-liturgy-field-btn-remove').click(function(event){
        event.preventDefault();
        $(this).parent().remove();
    });

    $('.pulpit-song-selectbox').select2({
        tags: true
    });
}


(function($){

    $(document).ready(function(){

        $('.pulpit-detailed-liturgy-form').sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                // reorder indices
                var index = 0;
                $(this).find('.pulpit-detailed-liturgy-form-single').each(function(){
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

        $('.pulpit-detailed-liturgy-btn-import').click(function(event) {
            event.preventDefault();
            $('#post').submit();
        });

        $('.pulpit-detailed-liturgy-field-btn-add').click(function(event){
            event.preventDefault();

            var index = $(this).parent().find('.pulpit-detailed-liturgy-form-single').length;
            var code = detailedLiturgyFormEmptyRecord[$(this).parent().data('key')];
            code = code.replace(/###INDEX###/g, index);

            $(this).before($(code));
            enableDetailedLiturgyFieldButtons()
        });

        $('.pulpit-detailed-liturgy-form-sub').toggle();
        $('.pulpit-detailed-liturgy-form-single-toggle').removeClass('ui-icon-arrowthick-1-n').addClass('ui-icon-arrowthick-1-s');
        enableDetailedLiturgyFieldButtons();
    });
})(jQuery);