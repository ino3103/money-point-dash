<div class="modal fade" id="transactionReportModal" tabindex="-1" aria-labelledby="transactionReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionReportModalLabel">Transaction Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.reports.transactions') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tx_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tx_start_date" name="start_date" required
                                value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tx_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tx_end_date" name="end_date" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tx_type" class="form-label">Transaction Type</label>
                            <select class="form-control select2" id="tx_type" name="type">
                                <option value="">All Types</option>
                                <option value="withdrawal">Withdrawal</option>
                                <option value="deposit">Deposit</option>
                                <option value="allocation">Allocation</option>
                                <option value="transfer">Transfer</option>
                                <option value="reconciliation">Reconciliation</option>
                                <option value="adjustment">Adjustment</option>
                                <option value="fee">Fee</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tx_teller_id" class="form-label">Teller</label>
                            <select class="form-control select2" id="tx_teller_id" name="teller_id">
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
                    <button type="submit" class="btn btn-success">
                        <i class="las la-file-pdf me-1"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


