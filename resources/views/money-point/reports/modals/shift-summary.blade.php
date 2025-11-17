<div class="modal fade" id="shiftSummaryModal" tabindex="-1" aria-labelledby="shiftSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shiftSummaryModalLabel">Shift Summary Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.reports.shift-summary') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required
                                value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="teller_id" class="form-label">Teller</label>
                            <select class="form-control select2" id="teller_id" name="teller_id">
                                <option value="">All Tellers</option>
                                @foreach ($tellers as $teller)
                                    <option value="{{ $teller->id }}">{{ $teller->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control select2" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="open">Open</option>
                                <option value="submitted">Submitted</option>
                                <option value="verified">Verified</option>
                                <option value="closed">Closed</option>
                                <option value="discrepancy">Discrepancy</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-file-pdf me-1"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


