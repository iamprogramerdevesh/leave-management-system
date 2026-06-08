(function ($) {
    'use strict';

    $('#login-form').on('submit', function (e) {
        e.preventDefault();

        var $btn = $('#login-btn');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Signing In...');

        $.ajax({
            url: '/login',
            type: 'POST',
            data: {
                login: $('#login').val(),
                password: $('#password').val(),
                remember: $('#remember').is(':checked') ? 1 : 0,
            },
            success: function (response) {
                showToast('success', response.message);
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1500);
            },
            error: function (xhr) {
                var message = 'Login failed. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join(' ');
                }

                showToast('error', message);
            },
            complete: function () {
                $btn.prop('disabled', false).text(originalText);
            },
        });
    });
})(jQuery);