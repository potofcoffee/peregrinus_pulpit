
var activeInput;


(function($){


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
            allowClear: true,
            placeholder: '',
            tags: true
        });
        $('.pulpit-song-selectbox')
            .on('select2:select', function(e){
                $('#'+$(this).parent().data('preview')+' .data-preview-song').html(e.params.data.text);
            });


        $('.pulpit-detailed-liturgy-form-single textarea').on('focus', function(){
            activeInput = this;
        });


        $('.pulpit-detailed-liturgy-field-btn-paste-template').click(function(event){
            event.preventDefault();

            $.post({
                url: ajaxurl,
                data: {
                    'action': 'pulpit_renderTextBlock',
                    'id': $(this).parent().find('select').first().val(),
                },
                context: this,
                success: function(response) {
                    $(activeInput).replaceSelectedText(response);
                    $(activeInput).trigger('change');
                }
            });



        });

        $('.pulpit-detailed-liturgy-extended-select').select2({});

    }


    function createSortableList() {
        $('.pulpit-detailed-liturgy-form').sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                // reorder indices
                $(this).find('.pulpit-detailed-liturgy-form-single').each(function(index, linkElement){
                    $(linkElement).find('input,select,textarea').each(function(subIndex, inputElement){
                        $(inputElement).attr('name', $(this).attr('name').replace(/(\d+)/g, index));
                        $(inputElement).attr('id', $(this).attr('id').replace(/(\d+)/g, index));
                    });
                    $(linkElement).find('label').each(function(subIndex, labelElement) {
                        $(labelElement).attr('for', $(this).attr('for').replace(/(\d+)/g, index));
                    });
                });
            }
        }).disableSelection();
    }


    $(document).ready(function(){

        createSortableList();

        $('.pulpit-detailed-liturgy-field-btn-add-item').click(function(event) {
            event.preventDefault();
            var title = window.prompt('Please enter the title for the new item');
            var count = $(this).parent().parent().find('li.pulpit-detailed-liturgy-form-single').length;
            var data = {
                'action': 'pulpit_createAgendaItemSubForm',
                'title': title,
                'index': count,
                'key': $(this).data('key'),
                'type': $(this).data('type')
            };
            $.post({
                url: ajaxurl,
                data: data,
                context: this,
                success: function(response) {
                    $(this).parent().parent().find('ul').append(response);
                    enableDetailedLiturgyFieldButtons();
                }
            });
            createSortableList();
        });

        $('.pulpit-detailed-liturgy-field-btn-import').click(function(event) {
            event.preventDefault();
            var count = $(this).parent().parent().find('li.pulpit-detailed-liturgy-form-single').length;
            var data = {
                'action': 'pulpit_importAgendaItems',
                'index': count,
                'source': $($(this).data('source')).val(),
                'key': $(this).data('key'),
                'officiating': $('#officiating').val(),
            };
            $.post({
                url: ajaxurl,
                data: data,
                context: this,
                success: function(response) {
                    $(this).parent().parent().find('ul').append(response);
                    enableDetailedLiturgyFieldButtons();
                    createSortableList();
                }
            });

        });

        $('.pulpit-detailed-liturgy-btn-import').click(function(event) {
            event.preventDefault();
            $('#post').submit();
        });

        $('.pulpit-detailed-liturgy-field-btn-remove-all').click(function(event) {
            event.preventDefault();
            $(this).parent().parent().find('li.pulpit-detailed-liturgy-form-single').remove();
        });


        $('.pulpit-detailed-liturgy-field-btn-add').click(function(event){
            event.preventDefault();

            var index = $(this).parent().find('.pulpit-detailed-liturgy-form-single').length;
            var code = detailedLiturgyFormEmptyRecord[$(this).parent().data('key')];
            code = code.replace(/###INDEX###/g, index);

            $(this).before($(code));
            enableDetailedLiturgyFieldButtons();
            createSortableList();
        });

        enableDetailedLiturgyFieldButtons();
        createSortableList();
    });
})(jQuery);