(function ($) {
    $(document).ready(function () {
        $('div.pulpit-slides-preview').sortable({
            placeholder: "ui-sortable-placeholder",
        });

        $('.open-slider-custom-modal').on('click', function () {

            tb_show('Slide editor', "#TB_inline?width=630&inlineId=slide-editor", false);
            $('#TB_ajaxContent').css('width', '').css('height', '');
            $('#slide-editor-image').click(function () {
                frame = wp.media({});
                frame.open();
            });
        });
    });
})(jQuery);