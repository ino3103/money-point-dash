<!-- Verify Shift Modal -->
<div class="modal fade" id="verifyShiftModal" tabindex="-1" aria-labelledby="verifyShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('money-point.shifts.verify.store', $shift->id) }}" method="POST" id="verifyShiftForm">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="verifyShiftModalLabel">
                        <i class="las la-check-double me-2 text-success"></i>Verify Shift
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 mb-4">
                        <i class="las la-info-circle me-2"></i>
                        <strong>Review Shift Details:</strong> Please review the expected vs reported balances before verifying this shift.
                    </div>

                    <!-- Shift Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <label class="form-label text-muted mb-1 small">Teller</label>
                                    <div class="fw-bold">{{ $shift->teller->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <label class="form-label text-muted mb-1 small">Status</label>
                                    <div>
                                        @php
                                            $statusText = ucwords($shift->status);
                                            $bgClass = match ($shift->status) {
                                                'submitted' => 'warning',
                                                'verified' => 'success',
                                                'discrepancy' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="bg-opacity-{{ $bgClass }} color-{{ $bgClass }} userDatatable-content-status active">{{ $statusText }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <label class="form-label text-muted mb-1 small">Mtaji (Opening Capital)</label>
                                    <div class="fw-bold text-success">
                                        @php
                                            $mtaji = $shift->opening_cash;
                                            if ($shift->opening_floats) {
                                                foreach ($shift->opening_floats as $amount) {
                                                    $mtaji += abs($amount);
                                                }
                                            }
                                        @endphp
                                        {{ formatCurrency($mtaji, 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Comparison -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-opacity-10 border-bottom">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="las la-money-bill-wave me-2"></i>Cash Reconciliation
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label text-muted mb-1 small">Expected Cash</label>
                                    <div class="fs-5 fw-bold text-info">{{ formatCurrency($shift->expected_closing_cash ?? 0, 0) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted mb-1 small">Reported Cash</label>
                                    <div class="fs-5 fw-bold text-success">{{ formatCurrency($shift->closing_cash ?? 0, 0) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted mb-1 small">Variance</label>
                                    <div class="fs-5 fw-bold text-{{ ($shift->variance_cash ?? 0) == 0 ? 'success' : 'danger' }}">
                                        {{ formatCurrency($shift->variance_cash ?? 0, 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Float Comparison -->
                    @if($shift->closing_floats && count($shift->closing_floats) > 0)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-opacity-10 border-bottom">
                                <h6 class="mb-0 fw-bold text-success">
                                    <i class="las la-mobile-alt me-2"></i>Float Reconciliation
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $providerNames = [];
                                    if ($shift->opening_floats) {
                                        foreach ($shift->opening_floats as $provider => $amount) {
                                            $providerModel = \App\Models\FloatProvider::where('name', $provider)->first();
                                            $providerNames[$provider] = $providerModel ? $providerModel->display_name : ucfirst($provider);
                                        }
                                    }
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Provider</th>
                                                <th class="text-end">Expected</th>
                                                <th class="text-end">Reported</th>
                                                <th class="text-end">Variance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($shift->closing_floats ?? [] as $provider => $reported)
                                                @php
                                                    $expected = $shift->expected_closing_floats[$provider] ?? 0;
                                                    $variance = $reported - $expected;
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $providerNames[$provider] ?? ucfirst($provider) }}</strong></td>
                                                    <td class="text-end text-info">{{ formatCurrency($expected, 0) }}</td>
                                                    <td class="text-end text-success">{{ formatCurrency($reported, 0) }}</td>
                                                    <td class="text-end text-{{ $variance == 0 ? 'success' : 'danger' }}">
                                                        {{ formatCurrency($variance, 0) }}
                                                        @if($variance != 0)
                                                            <small class="d-block">({{ $variance > 0 ? 'Over' : 'Short' }})</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Verification Action -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="verify_action" class="form-label fw-bold mb-2">
                                Action <span class="text-danger">*</span>
                            </label>
                            <select class="form-control ih-medium ip-gray radius-xs b-light px-15" id="verify_action" name="action" required>
                                <option value="" selected disabled>Select Action</option>
                                <option value="approve">Approve & Close Shift</option>
                                <option value="request_adjustment">Request Adjustment</option>
                            </select>
                        </div>
                    </div>

                    <!-- Adjustments Section (hidden by default) -->
                    <div class="row mb-3" id="adjustments-section" style="display: none;">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">Adjustments</label>
                            <div id="adjustments-container">
                                <!-- Adjustments will be added dynamically -->
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-adjustment">
                                <i class="las la-plus me-1"></i>Add Adjustment
                            </button>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="verify_notes" class="form-label fw-bold mb-2">
                                <i class="las la-sticky-note me-1 text-info"></i>Notes (Optional)
                            </label>
                            <textarea class="form-control" id="verify_notes" name="notes" rows="3" 
                                placeholder="Enter any notes about the verification..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="las la-check-double me-1"></i>Verify Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

