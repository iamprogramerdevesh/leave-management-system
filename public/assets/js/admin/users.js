(function ($) {
    'use strict';

    var usersTable;
    var searchTimer;

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
        var badgeClass = status === 'active' ? 'badge-success' : 'badge-secondary';

        return '<span class="badge ' + badgeClass + '">' + status + '</span>';
    }

    function roleLabel(role) {
        return role.charAt(0).toUpperCase() + role.slice(1);
    }

    function renderActions(row) {
        var isSelf = row.id === window.currentUserId;
        var statusBtn = '';

        if (!isSelf) {
            if (row.status === 'active') {
                statusBtn = '<button class="btn btn-sm btn-warning btn-status" data-id="' + row.id + '" data-status="inactive" title="Deactivate"><i class="fas fa-ban"></i></button>';
            } else {
                statusBtn = '<button class="btn btn-sm btn-success btn-status" data-id="' + row.id + '" data-status="active" title="Activate"><i class="fas fa-check"></i></button>';
            }
        }

        return '<button class="btn btn-sm btn-info btn-edit" data-id="' + row.id + '" title="Edit"><i class="fas fa-edit"></i></button> ' + statusBtn;
    }

    function initDataTable() {
        usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: false,
            searching: false,
            lengthChange: true,
            ajax: {
                url: window.userRoutes.list,
                data: function () {
                    return {
                        search: $('#user-search').val()
                    };
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'employee_id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'department' },
                { data: 'designation' },
                {
                    data: 'role',
                    render: function (data) {
                        return roleLabel(data);
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

    function resetForm() {
        $('#user-form')[0].reset();
        $('#user-id').val('');
        $('#password').prop('required', true);
        $('#password-hint').text('Required for new users.');
        $('#user-modal-title').text('Add User');
    }

    function openCreateModal() {
        resetForm();
        $('#user-modal').modal('show');
    }

    function openEditModal(user) {
        resetForm();
        $('#user-id').val(user.id);
        $('#employee_id').val(user.employee_id);
        $('#name').val(user.name);
        $('#email').val(user.email);
        $('#mobile').val(user.mobile);
        $('#department').val(user.department);
        $('#designation').val(user.designation);
        $('#role').val(user.role);
        $('#status').val(user.status);
        $('#password').prop('required', false);
        $('#password-hint').text('Leave blank to keep current password.');
        $('#user-modal-title').text('Edit User');
        $('#user-modal').modal('show');
    }

    function reloadTable() {
        usersTable.ajax.reload(null, false);
    }

    $('#user-search').on('keyup', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            reloadTable();
        }, 300);
    });

    $('#btn-add-user').on('click', openCreateModal);

    $('#users-table').on('click', '.btn-edit', function () {
        var userId = $(this).data('id');

        $.get(window.userRoutes.show + '/' + userId, function (response) {
            openEditModal(response.data);
        }).fail(function (xhr) {
            showAlert('error', getValidationMessage(xhr));
        });
    });

    $('#users-table').on('click', '.btn-status', function () {
        var userId = $(this).data('id');
        var status = $(this).data('status');
        var actionLabel = status === 'active' ? 'activate' : 'deactivate';

        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to ' + actionLabel + ' this user.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + actionLabel
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: window.userRoutes.status + '/' + userId + '/status',
                type: 'PATCH',
                data: { status: status },
                success: function (response) {
                    showAlert('success', response.message, reloadTable);
                },
                error: function (xhr) {
                    showAlert('error', getValidationMessage(xhr));
                }
            });
        });
    });

    $('#user-form').on('submit', function (e) {
        e.preventDefault();

        var userId = $('#user-id').val();
        var isEdit = userId !== '';
        var url = isEdit ? window.userRoutes.update + '/' + userId : window.userRoutes.store;
        var method = isEdit ? 'PUT' : 'POST';
        var $btn = $('#btn-save-user');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: method,
            data: {
                employee_id: $('#employee_id').val(),
                name: $('#name').val(),
                email: $('#email').val(),
                mobile: $('#mobile').val(),
                department: $('#department').val(),
                designation: $('#designation').val(),
                role: $('#role').val(),
                status: $('#status').val(),
                password: $('#password').val()
            },
            success: function (response) {
                $('#user-modal').modal('hide');
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