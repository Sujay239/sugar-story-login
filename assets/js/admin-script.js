jQuery(document).ready(function($) {
    
    // Initialize color pickers
    if ($('.sugar-color-picker').length) {
        $('.sugar-color-picker').wpColorPicker();
    }

    // Media uploader logic
    var file_frame;

    $('.upload_image_button').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var targetId = button.data('target');
        var targetInput = $('#' + targetId);
        var targetPreview = $('#image-preview-' + targetId.replace('sugar_story_image_', ''));

        // If the media frame already exists, reopen it.
        if (file_frame) {
            // file_frame.open(); // Actually, better to create a new one to bind to the specific button click correctly if using single variable, or just create new each time for simplicity.
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload an Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            
            // Set the input value to the attachment URL
            targetInput.val(attachment.url);
            
            // Update the preview image
            targetPreview.attr('src', attachment.url).show();
        });

        // Finally, open the modal
        file_frame.open();
    });

    // Clear image logic
    $('.clear_image_button').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var targetId = button.data('target');
        var targetInput = $('#' + targetId);
        var targetPreview = $('#image-preview-' + targetId.replace('sugar_story_image_', ''));

        targetInput.val('');
        targetPreview.attr('src', '').hide();
    });

});
