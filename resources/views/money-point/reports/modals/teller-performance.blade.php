<div class="modal fade" id="tellerPerformanceModal" tabindex="-1" aria-labelledby="tellerPerformanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tellerPerformanceModalLabel">Teller Performance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.reports.teller-performance') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="perf_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="perf_start_date" name="start_date" required
                                value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="perf_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="perf_end_date" name="end_date" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="las la-info-circle me-1"></i>
                        This report shows performance metrics for all tellers including:
                        <ul class="mb-0 mt-2">
                            <li>Number of shifts and completion rate</li>
                            <li>Total transactions processed</li>
                            <li>Deposits and withdrawals totals</li>
                            <li>Net flow per teller</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="las la-file-pdf me-1"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


