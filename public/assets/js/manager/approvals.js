(function ($) {
    'use strict';

    var approvalsTable;

    function getValidationMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        if (xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.values(xhr.responseJSON.errors).flat().join(' ');
        }

        return 'Something went wrong. Please try again.';
    }

    function statusBadge(status) {
        var map = {
            'pending': 'badge-warning',
            'approved': 'badge-success',
            'rejected': 'badge-danger',
        };

        return '<span class="badge ' + (map[status] || 'badge-secondary') + '">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }

    function renderActions(row) {
        if (row.status !== 'pending') {
            return '<span class="text-muted">Processed</span>';
        }

        return '<button class="btn btn-sm btn-success btn-approve" data-id="' + row.id + '" title="Approve"><i class="fas fa-check"></i></button> ' +
               '<button class="btn btn-sm btn-danger btn-reject" data-id="' + row.id + '" title="Reject"><i class="fas fa-times"></i></button>';
    }

    function getFilterData() {
        return {
            leave_type_id: $('#filter-leave-type').val(),
            status: $('#filter-status').val(),
        };
    }

    function initDataTable() {
        approvalsTable = $('#approvals-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            lengthChange: true,
            order: [[0, 'desc']],
            ajax: {
                url: window.approvalRoutes.list,
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
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return renderActions(row);
                    }
                }
            ]
        });
    }

    function reloadTable() {
        approvalsTable.ajax.reload(null, false);
    }

    function openApprovalModal(leaveRequestId, action) {
        var row = approvalsTable.row(function (idx, data, node) {
            return data.id === leaveRequestId;
        }).data();

        if (!row) return;

        $('#leave-request-id').val(leaveRequestId);
        $('#action-type').val(action);
        $('#modal-employee').text(row.employee + ' (' + row.employee_id + ')');
        $('#modal-leave-type').text(row.leave_type);
        $('#modal-dates').text(row.start_date + ' to ' + row.end_date);
        $('#modal-duration').text(row.duration + (row.duration === 1 ? ' day' : ' days'));
        $('#modal-reason').text(row.reason || '-');
        $('#manager_remarks').val('');

        var actionLabel = action === 'approve' ? 'Approve' : 'Reject';
        var btnClass = action === 'approve' ? 'btn-success' : 'btn-danger';
        $('#approval-modal-title').text(actionLabel + ' Leave Request');
        $('#btn-confirm-action').text('Yes, ' + actionLabel).removeClass('btn-success btn-danger').addClass(btnClass);

        $('#approval-modal').modal('show');
    }

    $('#approvals-table').on('click', '.btn-approve', function () {
        openApprovalModal($(this).data('id'), 'approve');
    });

    $('#approvals-table').on('click', '.btn-reject', function () {
        openApprovalModal($(this).data('id'), 'reject');
    });

    $('#approval-form').on('submit', function (e) {
        e.preventDefault();

        var leaveRequestId = $('#leave-request-id').val();
        var action = $('#action-type').val();
        var $btn = $('#btn-confirm-action');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Processing...');

        var url = window.approvalRoutes.approve + '/' + leaveRequestId + '/' + action;

        $.ajax({
            url: url,
            type: 'PUT',
            data: {
                manager_remarks: $('#manager_remarks').val()
            },
            success: function (response) {
                $('#approval-modal').modal('hide');
                showAlert('success', response.message, reloadTable);
            },
            error: function (xhr) {
                showAlert('error', getValidationMessage(xhr));
            },
            complete: function () {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });

    $('#btn-filter').on('click', reloadTable);

    $('#btn-reset').on('click', function () {
        $('#filter-leave-type').val('');
        $('#filter-status').val('');
        reloadTable();
    });

    initDataTable();
})(jQuery);