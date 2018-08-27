
function enableEventsRelationFieldsRemoveButtons() {
    $('.events-relation-field-btn-remove').click(function(event){
        event.preventDefault();
        $(this).parent().remove();
    });
}


(function($){

    $(document).ready(function(){
        $('.events-relation-field-btn-add').click(function(event){
            event.preventDefault();

            var index = $(this).parent().find('.events-relation-form-single').length;
            var code = eventRelationFormEmptyRecord[$(this).parent().data('key')];
            code = code.replace(/###INDEX###/g, index);

            $(this).before($(code));
            enableEventsRelationFieldsRemoveButtons()
        });

        enableEventsRelationFieldsRemoveButtons();
    });
})(jQuery);