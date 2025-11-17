<!-- Close Shift Modal -->
<div class="modal fade" id="submitShiftModal" tabindex="-1" aria-labelledby="submitShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('money-point.shifts.submit.store', $shift->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="submitShiftModalLabel">
                        <i class="las la-check-circle me-2 text-warning"></i>Close Shift
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Compact Header with Mtaji -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <small class="text-muted d-block mb-1">Mtaji (Opening Capital)</small>
                                    <strong class="text-success">{{ formatCurrency($mtaji, 0) }}</strong>
                                </div>
                                <div class="text-center">
                                    <small class="text-muted d-block mb-1">Expected Closing Capital</small>
                                    <strong class="text-info">{{ formatCurrency($mtaji, 0) }}</strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block mb-1">Balanced (Closing Capital)</small>
                                    <strong class="text-primary" id="calculatedBalanced">TZS 0</strong>
                                </div>
                            </div>
                            <div id="balancedStatus" class="alert mt-2 mb-0 py-2">

                                <span id="balancedStatusText">Enter closing amounts to see if balanced</span>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content: Opening vs Closing in Table Format -->
                    <div class="row">
                        <!-- Opening Balances Column -->
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-opacity-10 border-bottom">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="las la-wallet me-2"></i>Opening Balances
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 pb-3 border-bottom">
                                        <label class="form-label text-muted mb-1 small">Opening Cash</label>
                                        <div class="fs-5 fw-bold text-primary">
                                            {{ formatCurrency($shift->opening_cash, 0) }}</div>
                                    </div>
                                    @if ($shift->opening_floats && count($shift->opening_floats) > 0)
                                        <div class="mb-2">
                                            <label class="form-label text-muted mb-2 small">Opening Floats</label>
                                            @foreach ($shift->opening_floats ?? [] as $provider => $amount)
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                    <span
                                                        class="text-muted small">{{ $providerNames[$provider] ?? ucfirst($provider) }}</span>
                                                    <strong
                                                        class="text-success">{{ formatCurrency(abs($amount), 0) }}</strong>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Closing Balances Column -->
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-opacity-10 border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="las la-edit me-2"></i>Closing Balances (Enter Actual Counts)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Closing Cash -->
                                    <div class="mb-3">
                                        <label for="closing_cash" class="form-label fw-bold mb-2">
                                            <i class="las la-money-bill-wave me-1 text-primary"></i>Closing Cash <span
                                                class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="las la-money-bill-wave text-primary"></i>
                                            </span>
                                            <input type="text" class="form-control amount" id="closing_cash"
                                                name="closing_cash" required placeholder="Enter actual cash count">
                                        </div>
                                        <small class="text-muted">
                                            Opening: <strong>{{ formatCurrency($shift->opening_cash, 0) }}</strong>
                                        </small>
                                    </div>

                                    <!-- Closing Floats -->
                                    @if ($shift->opening_floats && count($shift->opening_floats) > 0)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold mb-2">
                                                <i class="las la-mobile-alt me-1 text-success"></i>Closing Floats <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="row g-2">
                                                @foreach ($shift->opening_floats ?? [] as $provider => $amount)
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="flex-grow-1 me-2">
                                                                <label for="closing_floats_{{ $provider }}"
                                                                    class="form-label mb-1 small text-muted">
                                                                    {{ $providerNames[$provider] ?? ucfirst($provider) }}
                                                                </label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text bg-white">
                                                                        <i class="las la-wallet text-success"></i>
                                                                    </span>
                                                                    <input type="text" class="form-control amount"
                                                                        id="closing_floats_{{ $provider }}"
                                                                        name="closing_floats[{{ $provider }}]"
                                                                        required placeholder="Enter balance">
                                                                </div>
                                                            </div>
                                                            <div class="text-end">
                                                                <small class="text-muted d-block small">Opening</small>
                                                                <strong
                                                                    class="text-success small">{{ formatCurrency(abs($amount), 0) }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Notes -->
                                    <div class="mt-3 pt-3 border-top">
                                        <label for="notes" class="form-label fw-bold mb-2 small">
                                            <i class="las la-sticky-note me-1 text-info"></i>Notes (Optional)
                                        </label>
                                        <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"
                                            placeholder="Any additional notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning fw-bold">
                        <i class="las la-check-circle me-1"></i>Close Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
