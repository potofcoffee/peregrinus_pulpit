var fileFrame;
var originalPostID = wp.media.model.settings.post.id; // Store the old id
var activeUploadField = null;

// namespace
var pulpit;
if (pulpit == null || pulpit == undefined) pulpit = {};
if (pulpit.admin == null || pulpit.admin == undefined) pulpit.admin = {};

pulpit.admin.uploader = {
    /**
     * Show media dialog
     * @param element
     */
    chooseMediaUpload: function (element) {
        activeUploadField = jQuery(element).parent();
        // If the media frame already exists, reopen it.
        // Set the wp.media post id so the uploader grabs the ID we want when initialised
        wp.media.model.settings.post.id = jQuery(this).data('attachment-id');
        // Create the media frame.
        var fileFrameOptions = {
            title: activeUploadField.data('dialog-title'),
            button: {
                text: activeUploadField.data('button-title'),
            },
            multiple: false	// Set to true to allow multiple files to be selected
        };
        if (activeUploadField.data('mime-type')) fileFrameOptions.library = { type : activeUploadField.data('mime-type')};
        if (activeUploadField.data('mime-type')) fileFrameOptions.library = { type : 'audio'};
        fileFrame = wp.media.frames.fileFrame = wp.media(fileFrameOptions);
        /**
         * Accept chosen media relation
         */
        fileFrame.on('select', function () {
            // We set multiple to false so only get one image from the uploader
            attachment = fileFrame.state().get('selection').first().toJSON();
            // Do something with attachment.id and/or attachment.url here
            jQuery('#pulpit-upload-data-' + activeUploadField.data('field')).val(attachment.id);
            jQuery('#pulpit-upload-preview-' + activeUploadField.data('field')).html('<div class="spinner is-active"></div>');
            // update preview via ajax
            jQuery.post(ajaxurl, {
                'action': 'pulpit_fieldPreview',
                'id': attachment.id,
                'field': activeUploadField.data('field')
            }, function (response) {
                data = JSON.parse(response);
                jQuery('#pulpit-upload-preview-' + data.field).html(data.preview);
            });
            pulpit.admin.uploader.checkUploadButtonVisibility();

            //$('#image-preview').attr('src', attachment.url).css('width', 'auto');
            //$('#image_attachment_id').val(attachment.id);
            // Restore the main post ID
            wp.media.model.settings.post.id = originalPostID;
        });
        // Finally, open the modal
        fileFrame.open();

    },

    /**
     * Hide upload buttons for all upload fields with existing value
     * For these fields, the media dialog can be triggered by clicking on the preview
     */
    checkUploadButtonVisibility: function () {
        jQuery('.pulpit-upload-wrapper').each(function () {
            if (jQuery('#pulpit-upload-data-' + jQuery(this).data('field')).val() == '') {
                jQuery(this).find('.pulpit-upload-button').show();
                jQuery(this).find('.pulpit-upload-preview').hide();
            } else {
                jQuery(this).find('.pulpit-upload-button').hide();
                jQuery(this).find('.pulpit-upload-preview').show();
            }
        });
    },
};


/**
 * Install click handlers
 */
jQuery(document).ready(function () {
    jQuery('.pulpit-hide-on-load').hide();

    /**
     * Handle delete button
     */
    jQuery('.pulpit-upload-clear-button').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        jQuery('#pulpit-upload-data-' + jQuery(this).data('field')).val('');
        jQuery('#pulpit-upload-preview-' + jQuery(this).data('field')).html('');
        pulpit.admin.uploader.checkUploadButtonVisibility();
        //jQuery(this).parent().find('.pulpit-upload-preview').hide();
        //jQuery(this).parent().find('.pulpit-upload-button').show();
    });

    /**
     * Handle upload button
     */
    jQuery('.pulpit-upload-button').click(function (event) {
        event.preventDefault();
        pulpit.admin.uploader.chooseMediaUpload(this);
    });

    /**
     * Handle click on preview area
     */
    jQuery('.pulpit-upload-preview').click(function (event) {
        event.preventDefault();
        pulpit.admin.uploader.chooseMediaUpload(this);
    });

    // Restore the main ID when the add media button is pressed
    jQuery('a.add_media').on('click', function () {
        wp.media.model.settings.post.id = originalPostID;
    });

});