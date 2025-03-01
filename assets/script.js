jQuery(document).ready(function ($) {

    const { __, _x, _n, _nx } = wp.i18n;

    if ($('.risbl-admin-color-picker').length) {
        $('.risbl-admin-color-picker').each(function () {
            $(this).wpColorPicker();
        });
    }

    $('.risbl-admin-media-upload-button').each(function () {
        $(this).on('click', function (e) {
            e.preventDefault();
            var button = $(this),
                targetField = $('#' + button.data('target')),
                allowedTypes = targetField.data('allowed-types') ? targetField.data('allowed-types').split(',') : [];

            // Create or reuse the media frame
            var frame = wp.media({
                title: __('Select or Upload Media', 'risbl-admin'),
                button: {
                    text: __('Use this media', 'risbl-admin'),
                },
                multiple: false,
                library: {
                    type: allowedTypes.length ? allowedTypes : undefined, // Restrict file types if specified
                },
            });

            // Handle selection
            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                targetField.val(attachment.url); // Set URL in hidden input
                $('#' + targetField.attr('id') + '-preview').html('<img src="' + attachment.url + '" alt="" style="max-width: 100px;"/>'); // Show preview
            });

            // Open media frame
            frame.open();
        });
    });

});
