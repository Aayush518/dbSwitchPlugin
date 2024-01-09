jQuery(document).ready(function($) {
    const bodyElement = $('body');
    const toggleButton = $('.dark-mode-toggle');

    function applyDarkMode() {
        bodyElement.addClass('dark-mode');
        updateLogoDisplay();
        setButtonText();
    }

    function removeDarkMode() {
        bodyElement.removeClass('dark-mode');
        updateLogoDisplay();
        setButtonText();
    }

    function updateLogoDisplay() {
        if (bodyElement.hasClass('dark-mode')) {
            $('.sdms-light-logo').hide();
            $('.sdms-dark-logo').show();
        } else {
            $('.sdms-dark-logo').hide();
            $('.sdms-light-logo').show();
        }
    }

    function setButtonText() {
        const buttonText = bodyElement.hasClass('dark-mode') ? toggleButton.data('enabled-text') : toggleButton.data('disabled-text');
        toggleButton.text(buttonText);
    }

    toggleButton.on('click', function() {
        if (bodyElement.hasClass('dark-mode')) {
            removeDarkMode();
            // Update the default dark mode setting in WordPress dashboard
            updateDefaultDarkModeSetting(0);
        } else {
            applyDarkMode();
            // Update the default dark mode setting in WordPress dashboard
            updateDefaultDarkModeSetting(1);
        }
    });

    // Initial logo display on page load
    updateLogoDisplay();

    // Set initial button text on page load
    setButtonText();

    // Function to update the default dark mode setting via AJAX
    function updateDefaultDarkModeSetting(value) {
        $.ajax({
            url: frontendajax.ajaxurl,
            method: 'POST',
            data: {
                action: 'update_default_dark_mode',
                value: value
            },
            success: function(response) {
                console.log("Default Dark Mode setting updated:", response);
            }
        });
    }
});
