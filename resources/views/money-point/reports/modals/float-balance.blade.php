<div class="modal fade" id="floatBalanceModal" tabindex="-1" aria-labelledby="floatBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="floatBalanceModalLabel">Float Balance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.reports.float-balance') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="provider_id" class="form-label">Float Provider</label>
                            <select class="form-control select2" id="provider_id" name="provider_id">
                                <option value="">All Providers</option>
                                @foreach (\App\Models\FloatProvider::getActive() as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="fb_teller_id" class="form-label">Teller</label>
                            <select class="form-control select2" id="fb_teller_id" name="teller_id">
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
                    <button type="submit" class="btn btn-info">
                        <i class="las la-file-pdf me-1"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


