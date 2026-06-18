jQuery(document).ready(function($) {
    // Show Register Form
    $('#show-register-form').on('click', function(e) {
        e.preventDefault();
        $('.sugar-login-section').fadeOut(300, function() {
            $('.sugar-register-section').fadeIn(300);
        });
    });

    // Show Login Form
    $('#show-login-form').on('click', function(e) {
        e.preventDefault();
        $('.sugar-register-section').fadeOut(300, function() {
            $('.sugar-login-section').fadeIn(300);
        });
    });
    
    // Automatically show register form if there are registration errors
    if ($('.woocommerce-error li').text().toLowerCase().indexOf('register') !== -1 || window.location.hash === '#register') {
        $('.sugar-login-section').hide();
        $('.sugar-register-section').show();
    }
});
