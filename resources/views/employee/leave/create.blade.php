@extends('employee.layout')

@section('title', 'Apply Leave | Leave Management System')

@section('content-header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Apply Leave</h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leave Application Form</h3>
                </div>
                <div class="card-body">
                    <form id="leave-form">
                        <div class="form-group">
                            <label for="leave_type_id">Leave Type</label>
                            <select class="form-control" id="leave_type_id" name="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>

                        <div class="form-group">
                            <label>Duration</label>
                            <p class="form-control-plaintext" id="duration-display">-- days</p>
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" maxlength="500"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" id="btn-submit-leave">
                            <i class="fas fa-paper-plane"></i> Submit Leave Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Leave Balances</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="sidebar-balance-table">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Used</th>
                                <th>Remaining</th>
                            </tr>
                        </thead>
                        <tbody id="sidebar-balance-tbody">
                            <tr>
                                <td colspan="3" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.leaveStoreUrl = '{{ route('employee.leave.store') }}';
        window.balanceUrl = '{{ route('employee.dashboard.stats') }}';
    </script>
    <script src="{{ url('assets/js/employee/leave.js') }}"></script>
@endpush