(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
        },
    });

    function getToastContainer() {
        var $container = $('#toast-container');
        if ($container.length === 0) {
            $container = $('<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
            $('body').append($container);
        }
        return $container;
    }

    window.showToast = function (type, message, callback) {
        var icons = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-times-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle',
        };

        var bgColors = {
            'success': '#28a745',
            'error': '#dc3545',
            'warning': '#ffc107',
            'info': '#17a2b8',
        };

        var $container = getToastContainer();
        var icon = icons[type] || icons.info;
        var bg = bgColors[type] || bgColors.info;

        var $toast = $(
            '<div class="toast-notification" style="' +
                'background: ' + bg + '; color: #fff; padding: 12px 20px; border-radius: 4px; ' +
                'margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); ' +
                'display: flex; align-items: center; gap: 10px; min-width: 280px; ' +
                'animation: slideInRight 0.3s ease; cursor: pointer;' +
            '">' +
                '<i class="' + icon + '" style="font-size: 20px;"></i>' +
                '<span style="flex: 1; font-size: 14px;">' + $('<span>').text(message).html() + '</span>' +
            '</div>'
        );

        $container.append($toast);

        setTimeout(function () {
            $toast.fadeOut(300, function () {
                $toast.remove();
                if (typeof callback === 'function') {
                    callback();
                }
            });
        }, 2000);

        $toast.on('click', function () {
            $toast.fadeOut(200, function () {
                $toast.remove();
            });
        });
    };

    // Alias showAlert to showToast to prevent Swal is not defined errors
    window.showAlert = function (type, message, callback) {
        window.showToast(type, message, callback);
    };

    // Inject keyframe animation
    var style = document.createElement('style');
    style.textContent = '@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
    document.head.appendChild(style);

    // Dark mode toggle function
    window.toggleDarkMode = function () {
        var body = document.body;
        body.classList.toggle('dark-mode');

        var isDark = body.classList.contains('dark-mode');
        localStorage.setItem('dark-mode', isDark ? '1' : '0');

        isDark ? $('.main-header').removeClass('navbar-white navbar-light').addClass('navbar-black navbar-dark') : $('.main-header').removeClass('navbar-black navbar-dark').addClass('navbar-white navbar-light');

        $('#dark-mode-icon').toggleClass('fa-moon fa-sun');
    };

    // Load dark mode preference
    $(document).ready(function () {
        if (localStorage.getItem('dark-mode') === '1') {
            document.body.classList.add('dark-mode');
            $('#dark-mode-icon').removeClass('fa-moon').addClass('fa-sun');
            $('.main-header').removeClass('navbar-white navbar-light').addClass('navbar-black navbar-dark')
        }
    });
})(jQuery);