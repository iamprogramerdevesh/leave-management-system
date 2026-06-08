(function ($) {
    'use strict';

    var leaveTypesTable;

    function getValidationMessage(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        if (xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.values(xhr.responseJSON.errors).flat().join(' ');
        }

        return 'Something went wrong. Please try again.';
    }

    function renderActions(row) {
        return '<button class="btn btn-sm btn-info btn-edit" data-id="' + row.id + '" title="Edit"><i class="fas fa-edit"></i></button>';
    }

    function initDataTable() {
        leaveTypesTable = $('#leave-types-table').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            lengthChange: true,
            ajax: {
                url: window.leaveTypeRoutes.list,
                dataSrc: 'data'
            },
            columns: [
                { data: 'name' },
                { data: 'default_allocation' },
                { data: 'created_at' },
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

    function resetForm() {
        $('#leave-type-form')[0].reset();
        $('#leave-type-id').val('');
        $('#leave-type-modal-title').text('Add Leave Type');
    }

    function openCreateModal() {
        resetForm();
        $('#leave-type-modal').modal('show');
    }

    function openEditModal(leaveType) {
        resetForm();
        $('#leave-type-id').val(leaveType.id);
        $('#name').val(leaveType.name);
        $('#default_allocation').val(leaveType.default_allocation);
        $('#leave-type-modal-title').text('Edit Leave Type');
        $('#leave-type-modal').modal('show');
    }

    function reloadTable() {
        leaveTypesTable.ajax.reload(null, false);
    }

    $('#btn-add-leave-type').on('click', openCreateModal);

    $('#leave-types-table').on('click', '.btn-edit', function () {
        var leaveTypeId = $(this).data('id');

        $.get(window.leaveTypeRoutes.show + '/' + leaveTypeId, function (response) {
            openEditModal(response.data);
        }).fail(function (xhr) {
            showAlert('error', getValidationMessage(xhr));
        });
    });

    $('#leave-type-form').on('submit', function (e) {
        e.preventDefault();

        var leaveTypeId = $('#leave-type-id').val();
        var isEdit = leaveTypeId !== '';
        var url = isEdit ? window.leaveTypeRoutes.update + '/' + leaveTypeId : window.leaveTypeRoutes.store;
        var method = isEdit ? 'PUT' : 'POST';
        var $btn = $('#btn-save-leave-type');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: method,
            data: {
                name: $('#name').val(),
                default_allocation: $('#default_allocation').val()
            },
            success: function (response) {
                $('#leave-type-modal').modal('hide');
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

    initDataTable();
})(jQuery);