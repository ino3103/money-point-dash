<!-- Withdrawal Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('money-point.transactions.withdraw.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="withdrawModalLabel">
                        <i class="las la-arrow-down me-2 text-danger"></i>Process Withdrawal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if(!$shift)
                        <div class="alert alert-warning border-0 mb-4">
                            <i class="las la-exclamation-triangle me-2"></i>
                            <strong>No Active Shift:</strong> You must have an open shift to perform withdrawals.
                            <a href="{{ route('money-point.shifts.create') }}" class="alert-link">Open a shift</a>
                        </div>
                    @else
                        <div class="alert alert-info border-0 mb-4">
                            <i class="las la-info-circle me-2"></i>
                            <strong>Active Shift:</strong> Processing withdrawal for your current shift.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="withdraw_provider" class="form-label fw-bold mb-2">
                                    Float Provider <span class="text-danger">*</span>
                                </label>
                                <select class="form-control ih-medium ip-gray radius-xs b-light px-15 select2" id="withdraw_provider" name="provider" required>
                                    <option value="" selected disabled>Select Provider</option>
                                    @foreach($floatAccounts as $account)
                                        @php
                                            $providerModel = \App\Models\FloatProvider::where('name', $account->provider)->first();
                                            $displayName = $providerModel ? $providerModel->display_name : ucfirst($account->provider);
                                        @endphp
                                        <option value="{{ $account->provider }}">
                                            {{ $displayName }} (Balance: {{ formatCurrency(abs($account->balance), 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="withdraw_amount" class="form-label fw-bold mb-2">
                                    Amount (TZS) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control amount ih-medium ip-gray radius-xs b-light px-15" 
                                    id="withdraw_amount" name="amount" required min="1" step="1" 
                                    placeholder="Enter withdrawal amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="withdraw_customer_phone" class="form-label fw-bold mb-2">
                                    Customer Phone
                                </label>
                                <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                    id="withdraw_customer_phone" name="customer_phone" 
                                    placeholder="Enter customer phone number">
                            </div>

                            <div class="col-md-6">
                                <label for="withdraw_reference" class="form-label fw-bold mb-2">
                                    Reference (Transaction ID)
                                </label>
                                <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                    id="withdraw_reference" name="reference" 
                                    placeholder="Enter transaction reference">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Cancel
                    </button>
                    @if($shift)
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="las la-arrow-down me-1"></i>Process Withdrawal
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

