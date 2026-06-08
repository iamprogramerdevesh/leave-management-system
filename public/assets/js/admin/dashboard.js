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
        $.get('/admin/dashboard/stats', function (response) {
            var data = response.data;

            $('#stat-total-requests').text(data.total_requests);
            $('#stat-approved').text(data.approved);
            $('#stat-pending').text(data.pending);
            $('#stat-rejected').text(data.rejected);
            $('#stat-total-users').text(data.total_users);
            $('#stat-active-users').text(data.active_users);
            $('#stat-total-managers').text(data.total_managers);
            $('#stat-total-employees').text(data.total_employees);
            $('#stat-total-balance').text(data.total_balance);

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
            var failEls = ['#stat-total-requests', '#stat-approved', '#stat-pending', '#stat-rejected',
                           '#stat-total-users', '#stat-active-users', '#stat-total-managers',
                           '#stat-total-employees', '#stat-total-balance'];
            $.each(failEls, function (i, id) { $(id).text('--'); });
            $('#recent-tbody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    $(document).ready(function () {
        loadStats();
    });
})(jQuery);