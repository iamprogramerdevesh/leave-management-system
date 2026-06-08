@extends('admin.layout')

@section('title', 'Leave Types | Leave Management System')

@push('styles')
    <link rel="stylesheet" href="{{ url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Leave Types</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-primary" id="btn-add-leave-type">
                        <i class="fas fa-plus"></i> Add Leave Type
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="leave-types-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Default Allocation (Days)</th>
                        <th>Created At</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="leave-type-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="leave-type-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leave-type-modal-title">Add Leave Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="leave-type-id" name="leave_type_id">

                        <div class="form-group">
                            <label for="name">Leave Type Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="default_allocation">Default Allocation (Days)</label>
                            <input type="number" class="form-control" id="default_allocation" name="default_allocation" min="1" max="365" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-leave-type">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        window.leaveTypeRoutes = {
            list: '{{ route('admin.leave-types.list') }}',
            store: '{{ route('admin.leave-types.store') }}',
            show: '{{ url('admin/leave-types') }}',
            update: '{{ url('admin/leave-types') }}',
        };
    </script>
    <script src="{{ url('assets/js/admin/leave-types.js') }}"></script>
@endpush