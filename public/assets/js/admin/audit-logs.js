(function ($) {
    'use strict';

    var auditTable;

    function getFilterData() {
        return {
            module: $('#filter-module').val(),
            action: $('#filter-action').val(),
            from_date: $('#filter-from').val(),
            to_date: $('#filter-to').val(),
        };
    }

    function initDataTable() {
        auditTable = $('#audit-logs-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            lengthChange: true,
            order: [[0, 'desc']],
            ajax: {
                url: window.auditRoutes.list,
                data: function () {
                    return getFilterData();
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'created_at' },
                { data: 'user' },
                { data: 'action' },
                { data: 'module' },
                { data: 'description' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<button class="btn btn-sm btn-info btn-view" data-id="' + row.id + '" title="View Details"><i class="fas fa-eye"></i></button>';
                    }
                }
            ]
        });
    }

    function reloadTable() {
        auditTable.ajax.reload(null, false);
    }

    $('#audit-logs-table').on('click', '.btn-view', function () {
        var logId = $(this).data('id');

        $.get(window.auditRoutes.show + '/' + logId, function (response) {
            
            var data = response.data;

            $('#detail-time').text(data.created_at);
            $('#detail-user').text(data.user);
            $('#detail-action').text(data.action);
            $('#detail-module').text(data.module);
            $('#detail-description').text(data.description || '-');

            var requestText = '';
            if (data.request && data.request !== 'null' && data.request !== null) {
                try {
                    var reqObj = JSON.parse(data.request);
                    requestText = JSON.stringify(reqObj, null, 2);
                } catch (e) {
                    requestText = data.request;
                }
            }
            $('#detail-request').val(requestText);

            var responseText = '';
            if (data.response && data.response !== 'null' && data.response !== null) {
                try {
                    var resObj = JSON.parse(data.response);
                    responseText = JSON.stringify(resObj, null, 2);
                } catch (e) {
                    responseText = data.response;
                }
            }
            $('#detail-response').val(responseText);

            $('#audit-detail-modal').modal('show');
        }).fail(function (xhr) {
            showAlert('error', 'Failed to load audit log details.');
        });
    });

    $('#btn-filter').on('click', reloadTable);

    $('#btn-reset').on('click', function () {
        $('#filter-module').val('');
        $('#filter-action').val('');
        $('#filter-from').val('');
        $('#filter-to').val('');
        reloadTable();
    });

    initDataTable();
})(jQuery);