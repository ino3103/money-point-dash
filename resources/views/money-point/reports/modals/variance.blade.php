<div class="modal fade" id="varianceModal" tabindex="-1" aria-labelledby="varianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varianceModalLabel">Variance/Discrepancy Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.reports.variance') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="las la-exclamation-triangle me-1"></i>
                        This report shows all shifts with discrepancies that require attention.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="var_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="var_start_date" name="start_date"
                                value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="var_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="var_end_date" name="end_date"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="var_teller_id" class="form-label">Teller</label>
                            <select class="form-control select2" id="var_teller_id" name="teller_id">
                                <option value="">All Tellers</option>
                                @foreach ($tellers as $teller)
                                    <option value="{{ $teller->id }}">{{ $teller->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="las la-file-pdf me-1"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


