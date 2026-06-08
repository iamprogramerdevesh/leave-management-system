@extends('manager.layout')

@section('title', 'Leave Approvals | Leave Management System')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Leave Approvals</h1>
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
                    <select class="form-control" id="filter-leave-type">
                        <option value="">All Leave Types</option>
                        @foreach ($leaveTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="filter-status">
                        <option value="">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
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
            <table id="approvals-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="approval-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="approval-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approval-modal-title">Process Leave Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="leave-request-id" name="leave_request_id">
                        <input type="hidden" id="action-type" name="action_type">

                        <div class="form-group">
                            <label>Employee</label>
                            <p class="form-control-plaintext" id="modal-employee"></p>
                        </div>

                        <div class="form-group">
                            <label>Leave Type</label>
                            <p class="form-control-plaintext" id="modal-leave-type"></p>
                        </div>

                        <div class="form-group">
                            <label>Dates</label>
                            <p class="form-control-plaintext" id="modal-dates"></p>
                        </div>

                        <div class="form-group">
                            <label>Duration</label>
                            <p class="form-control-plaintext" id="modal-duration"></p>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <p class="form-control-plaintext" id="modal-reason"></p>
                        </div>

                        <div class="form-group">
                            <label for="manager_remarks">Manager Remarks</label>
                            <textarea class="form-control" id="manager_remarks" name="manager_remarks" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="btn-confirm-action">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ url('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        window.approvalRoutes = {
            list: '{{ route('manager.approvals.list') }}',
            approve: '{{ url('manager/approvals') }}',
            reject: '{{ url('manager/approvals') }}',
        };
    </script>
    <script src="{{ url('assets/js/manager/approvals.js') }}"></script>
@endpush