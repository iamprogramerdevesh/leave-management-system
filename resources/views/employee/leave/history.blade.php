@extends('employee.layout')

@section('title', 'Leave History | Leave Management System')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Leave History</h1>
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
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="filter-from" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" id="filter-to" placeholder="To Date">
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
            <table id="leave-history-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
        window.historyListUrl = '{{ route('employee.leave.list') }}';
    </script>
    <script src="{{ url('assets/js/employee/leave-history.js') }}"></script>
@endpush