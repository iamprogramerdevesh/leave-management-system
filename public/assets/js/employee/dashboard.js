(function ($) {
    'use strict';

    function loadStats() {
        $.get('/employee/dashboard/stats', function (response) {
            var data = response.data;

            $('#stat-total-requests').text(data.total_requests);
            $('#stat-approved').text(data.approved);
            $('#stat-pending').text(data.pending);
            $('#stat-rejected').text(data.rejected);
            $('#stat-total-balance').text(data.total_balance);

            var tbody = $('#balance-tbody');
            tbody.empty();

            if (data.balance_details.length === 0) {
                tbody.html('<tr><td colspan="4" class="text-center">No balance records found.</td></tr>');
                return;
            }

            $.each(data.balance_details, function (i, item) {
                var badgeClass = 'badge-success';
                if (item.remaining <= 0) {
                    badgeClass = 'badge-danger';
                } else if (item.remaining <= 2) {
                    badgeClass = 'badge-warning';
                }

                tbody.append(
                    '<tr>' +
                        '<td>' + item.leave_type + '</td>' +
                        '<td>' + item.allocated + '</td>' +
                        '<td>' + item.used + '</td>' +
                        '<td><span class="badge ' + badgeClass + '">' + item.remaining + '</span></td>' +
                    '</tr>'
                );
            });
        }).fail(function () {
            $('#stat-total-requests').text('--');
            $('#stat-approved').text('--');
            $('#stat-pending').text('--');
            $('#stat-rejected').text('--');
            $('#stat-total-balance').text('--');
            $('#balance-tbody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    $(document).ready(function () {
        loadStats();
    });
})(jQuery);