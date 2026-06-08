(function ($) {
    'use strict';

    var reportTable;

    function statusBadge(status) {
        var map = {
            'pending': 'badge-warning',
            'approved': 'badge-success',
            'rejected': 'badge-danger',
        };
        return '<span class="badge ' + (map[status] || 'badge-secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }

    function getFilterData() {
        return {
            user_id: $('#filter-employee').val(),
            leave_type_id: $('#filter-leave-type').val(),
            status: $('#filter-status').val(),
            from_date: $('#filter-from').val(),
            to_date: $('#filter-to').val(),
        };
    }

    function loadSummary() {
        $.get(window.reportRoutes.summary, function (response) {
            var data = response.data;
            $('#summary-total-requests').text(data.total_requests);
            $('#summary-approved').text(data.approved);
            $('#summary-pending').text(data.pending);
            $('#summary-rejected').text(data.rejected);
            $('#summary-users').text(data.total_users);
            $('#summary-days').text(data.total_days_approved);

            var tbody = $('#breakdown-tbody');
            tbody.empty();
            if (data.leave_type_breakdown.length === 0) {
                tbody.html('<tr><td colspan="2" class="text-center">No data.</td></tr>');
            } else {
                $.each(data.leave_type_breakdown, function (i, item) {
                    tbody.append('<tr><td>' + item.leave_type + '</td><td>' + item.total_requests + '</td></tr>');
                });
            }
        }).fail(function () {
            var ids = ['#summary-total-requests', '#summary-approved', '#summary-pending',
                       '#summary-rejected', '#summary-users', '#summary-days'];
            $.each(ids, function (i, id) { $(id).text('--'); });
            $('#breakdown-tbody').html('<tr><td colspan="2" class="text-center text-danger">Failed to load.</td></tr>');
        });
    }

    function initDataTable() {
        reportTable = $('#report-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            lengthChange: true,
            order: [[0, 'desc']],
            ajax: {
                url: window.reportRoutes.list,
                data: function () {
                    return getFilterData();
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'employee' },
                { data: 'employee_id' },
                { data: 'department' },
                { data: 'leave_type' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'duration' },
                {
                    data: 'status',
                    render: function (data) {
                        return statusBadge(data);
                    }
                },
                {
                    data: 'approved_by',
                    render: function (data) { return data || '-'; }
                },
                {
                    data: 'approved_at',
                    render: function (data) { return data || '-'; }
                }
            ]
        });
    }

    function reloadTable() {
        reportTable.ajax.reload(null, false);
    }

    $('#btn-filter').on('click', reloadTable);

    $('#btn-reset').on('click', function () {
        $('#filter-employee').val('');
        $('#filter-leave-type').val('');
        $('#filter-status').val('');
        $('#filter-from').val('');
        $('#filter-to').val('');
        reloadTable();
    });

    initDataTable();
    loadSummary();
})(jQuery);