<!-- Reject Shift Modal -->
<div class="modal fade" id="rejectShiftModal" tabindex="-1" aria-labelledby="rejectShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="rejectShiftModalLabel">
                    <i class="las la-times-circle text-danger me-2"></i>Reject Shift
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('money-point.shifts.reject', $shift->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="las la-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Rejecting this shift will prevent you from continuing with transactions until the treasurer reviews and corrects the issues.
                    </div>

                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                            id="rejection_reason" 
                            name="rejection_reason" 
                            rows="5" 
                            placeholder="Please provide a detailed reason for rejecting this shift. This will help the treasurer understand what needs to be corrected..."
                            required
                            minlength="10"
                            maxlength="1000">{{ old('rejection_reason') }}</textarea>
                        <div class="form-text">
                            Minimum 10 characters. Please be specific about what is wrong or what needs to be corrected.
                        </div>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <h6 class="fw-bold mb-2">Shift Summary:</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Closing Cash:</small>
                                <strong>{{ formatCurrency($shift->closing_cash ?? 0, 0) }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Expected Cash:</small>
                                <strong>{{ formatCurrency($shift->expected_closing_cash ?? 0, 0) }}</strong>
                            </div>
                        </div>
                        @if ($shift->variance_cash != 0)
                            <div class="mt-2">
                                <small class="text-muted d-block">Variance:</small>
                                <strong class="text-danger">{{ formatCurrency(abs($shift->variance_cash), 0) }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary btn-sm btn-default btn-squared" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm btn-default btn-squared">
                        <i class="las la-times-circle me-1"></i>Reject Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

