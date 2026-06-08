@extends('employee.layout')

@section('title', 'Employee Dashboard | Leave Management System')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row" id="stats-container">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="stat-total-requests">0</h3>
                    <p>Total Leave Requests</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clipboard"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="stat-approved">0</h3>
                    <p>Approved</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="stat-pending">0</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="stat-rejected">0</h3>
                    <p>Rejected</p>
                </div>
                <div class="icon">
                    <i class="ion ion-close"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="stat-total-balance">0</h3>
                    <p>Remaining Balance (Days)</p>
                </div>
                <div class="icon"><i class="ion ion-calendar"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leave Balance Details</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="balance-table">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Allocated</th>
                                <th>Used</th>
                                <th>Remaining</th>
                            </tr>
                        </thead>
                        <tbody id="balance-tbody">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('assets/js/employee/dashboard.js') }}"></script>
@endpush