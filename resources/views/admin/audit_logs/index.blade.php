@extends('admin.layout')

@section('title', 'Audit Logs | Leave Management System')

@push('styles')
    <link rel="stylesheet" href="{{ url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Audit Logs</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <select class="form-control" id="filter-module">
                        <option value="">All Modules</option>
                        <option value="auth">Auth</option>
                        <option value="user_management">User Management</option>
                        <option value="leave">Leave</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="filter-action">
                        <option value="">All Actions</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="status_update">Status Update</option>
                        <option value="apply">Apply Leave</option>
                        <option value="approve">Approve</option>
                        <option value="reject">Reject</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="filter-from" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="filter-to" placeholder="To">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" id="btn-filter">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <button class="btn btn-secondary" id="btn-reset">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="audit-logs-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th width="100">Details</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="audit-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Log Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date/Time</label>
                        <p class="form-control-plaintext" id="detail-time"></p>
                    </div>
                    <div class="form-group">
                        <label>User</label>
                        <p class="form-control-plaintext" id="detail-user"></p>
                    </div>
                    <div class="form-group">
                        <label>Action</label>
                        <p class="form-control-plaintext" id="detail-action"></p>
                    </div>
                    <div class="form-group">
                        <label>Module</label>
                        <p class="form-control-plaintext" id="detail-module"></p>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <p class="form-control-plaintext" id="detail-description"></p>
                    </div>
                    <div class="form-group">
                        <label>Request Data</label>
                        <textarea class="form-control" id="detail-request" rows="5" readonly style="resize: vertical; font-family: monospace; font-size: 13px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Response Data</label>
                        <textarea class="form-control" id="detail-response" rows="5" readonly style="resize: vertical; font-family: monospace; font-size: 13px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        window.auditRoutes = {
            list: '{{ route('admin.audit-logs.list') }}',
            show: '{{ url('admin/audit-logs') }}',
        };
    </script>
    <script src="{{ url('assets/js/admin/audit-logs.js') }}"></script>
@endpush