@extends('admin.layout')

@section('title', 'Reports | Leave Management System')

@push('styles')
    <link rel="stylesheet" href="{{ url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Leave Reports</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row" id="summary-container">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="summary-total-requests">0</h3>
                    <p>Total Requests</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="summary-approved">0</h3>
                    <p>Approved</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="summary-pending">0</h3>
                    <p>Pending</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="summary-rejected">0</h3>
                    <p>Rejected</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="summary-users">0</h3>
                    <p>Active Users</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="summary-days">0</h3>
                    <p>Days Approved</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leave Type Breakdown</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="breakdown-table">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Total Requests</th>
                            </tr>
                        </thead>
                        <tbody id="breakdown-tbody">
                            <tr><td colspan="2" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <select class="form-control" id="filter-employee">
                        <option value="">All Employees</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
                    <input type="date" class="form-control" id="filter-from" placeholder="From">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filter-to" placeholder="To">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
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
            <table id="report-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Approved At</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        window.reportRoutes = {
            list: '{{ route('admin.reports.list') }}',
            summary: '{{ route('admin.reports.summary') }}',
        };
    </script>
    <script src="{{ url('assets/js/admin/reports.js') }}"></script>
@endpush