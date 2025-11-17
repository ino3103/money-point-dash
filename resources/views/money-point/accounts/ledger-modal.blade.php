<!-- Account Ledger Modal -->
<div class="modal fade" id="ledgerModal" tabindex="-1" aria-labelledby="ledgerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="ledgerModalLabel">
                    <i class="las la-book me-2 text-primary"></i>Account Ledger
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="ledgerContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading ledger...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="las la-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
