(function ($) {
    'use strict';

    function statusBadge(status) {
        var map = {
            'pending': 'badge-warning',
            'approved': 'badge-success',
            'rejected': 'badge-danger',
        };

        return '<span class="badge ' + (map[status] || 'badge-secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }

    function loadStats() {
        $.get('/manager/dashboard/stats', function (response) {
            var data = response.data;

            $('#stat-total-requests').text(data.total_requests);
            $('#stat-approved').text(data.approved);
            $('#stat-pending').text(data.pending);
            $('#stat-rejected').text(data.rejected);

            var tbody = $('#recent-tbody');
            tbody.empty();

            if (data.recent_requests.length === 0) {
                tbody.html('<tr><td colspan="5" class="text-center">No leave requests found.</td></tr>');
                return;
            }

            $.each(data.recent_requests, function (i, item) {
                tbody.append(
                    '<tr>' +
                        '<td>' + item.employee + '</td>' +
                        '<td>' + item.leave_type + '</td>' +
                        '<td>' + item.start_date + ' to ' + item.end_date + '</td>' +
                        '<td>' + item.duration + '</td>' +
                        '<td>' + statusBadge(item.status) + '</td>' +
                    '</tr>'
                );
            });
        }).fail(function () {
            $('#stat-total-requests').text('--');
            $('#stat-approved').text('--');
            $('#stat-pending').text('--');
            $('#stat-rejected').text('--');
            $('#recent-tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    $(document).ready(function () {
        loadStats();
    });
})(jQuery);