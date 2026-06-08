(function ($) {
    'use strict';

    function loadBalances() {
        $.get(window.balanceUrl, function (response) {
            var tbody = $('#sidebar-balance-tbody');
            tbody.empty();

            var details = response.data.balance_details;

            if (details.length === 0) {
                tbody.html('<tr><td colspan="3" class="text-center">No balances found.</td></tr>');
                return;
            }

            $.each(details, function (i, item) {
                var badgeClass = 'badge-success';
                if (item.remaining <= 0) {
                    badgeClass = 'badge-danger';
                } else if (item.remaining <= 2) {
                    badgeClass = 'badge-warning';
                }

                tbody.append(
                    '<tr>' +
                        '<td>' + item.leave_type + '</td>' +
                        '<td>' + item.used + '</td>' +
                        '<td><span class="badge ' + badgeClass + '">' + item.remaining + '</span></td>' +
                    '</tr>'
                );
            });
        }).fail(function () {
            $('#sidebar-balance-tbody').html(
                '<tr><td colspan="3" class="text-center text-danger">Failed to load.</td></tr>'
            );
        });
    }

    function calculateDuration() {
        var start = $('#start_date').val();
        var end = $('#end_date').val();

        if (!start || !end) {
            $('#duration-display').text('-- days');
            return;
        }

        if (end < start) {
            $('#duration-display').text('Invalid date range');
            return;
        }

        var s = new Date(start);
        var e = new Date(end);
        var diff = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;

        $('#duration-display').text(diff + (diff === 1 ? ' day' : ' days'));
    }

    function getValidationMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        if (xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.values(xhr.responseJSON.errors).flat().join(' ');
        }

        return 'Something went wrong. Please try again.';
    }

    $('#start_date, #end_date').on('change', calculateDuration);

    $('#leave-form').on('submit', function (e) {
        e.preventDefault();

        var $btn = $('#btn-submit-leave');
        var originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url: window.leaveStoreUrl,
            type: 'POST',
            data: {
                leave_type_id: $('#leave_type_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                reason: $('#reason').val()
            },
            success: function (response) {
                showAlert('success', response.message, function () {
                    window.location.href = '/employee';
                });
            },
            error: function (xhr) {
                showAlert('error', getValidationMessage(xhr));
            },
            complete: function () {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    $(document).ready(function () {
        loadBalances();

        var today = new Date().toISOString().split('T')[0];
        $('#start_date').attr('min', today);
        $('#end_date').attr('min', today);
    });
})(jQuery);