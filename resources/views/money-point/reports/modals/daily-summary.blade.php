<div class="modal fade" id="dailySummaryModal" tabindex="-1" aria-labelledby="dailySummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dailySummaryModalLabel">Daily Summary Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.reports.daily-summary') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="summary_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="summary_date" name="date" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="las la-info-circle me-1"></i>
                        This report includes daily deposits, withdrawals, net flow, and shift statistics.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="las la-file-pdf me-1"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


