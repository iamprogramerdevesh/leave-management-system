(function ($) {
    'use strict';

    var historyTable;

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
            leave_type_id: $('#filter-leave-type').val(),
            status: $('#filter-status').val(),
            from_date: $('#filter-from').val(),
            to_date: $('#filter-to').val(),
        };
    }

    function initDataTable() {
        historyTable = $('#leave-history-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            lengthChange: true,
            order: [[0, 'desc']],
            ajax: {
                url: window.historyListUrl,
                data: function () {
                    return getFilterData();
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'leave_type' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'duration' },
                {
                    data: 'reason',
                    render: function (data) {
                        return data || '-';
                    }
                },
                {
                    data: 'status',
                    render: function (data) {
                        return statusBadge(data);
                    }
                },
                {
                    data: 'approved_by',
                    render: function (data) {
                        return data || '-';
                    }
                },
                {
                    data: 'manager_remarks',
                    render: function (data) {
                        return data || '-';
                    }
                }
            ]
        });
    }

    function reloadTable() {
        historyTable.ajax.reload(null, false);
    }

    $('#btn-filter').on('click', reloadTable);

    $('#btn-reset').on('click', function () {
        $('#filter-leave-type').val('');
        $('#filter-status').val('');
        $('#filter-from').val('');
        $('#filter-to').val('');
        reloadTable();
    });

    initDataTable();
})(jQuery);