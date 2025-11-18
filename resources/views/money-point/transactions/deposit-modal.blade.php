<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('money-point.transactions.deposit.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="depositModalLabel">
                        <i class="las la-arrow-up me-2 text-success"></i>Process Deposit
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if (!$shift)
                        <div class="alert alert-warning border-0 mb-4">
                            <i class="las la-exclamation-triangle me-2"></i>
                            <strong>No Active Shift:</strong> You must have an open shift to perform deposits.
                            <a href="{{ route('money-point.shifts.create') }}" class="alert-link">Open a shift</a>
                        </div>
                    @else
                        <div class="alert alert-info border-0 mb-4">
                            <i class="las la-info-circle me-2"></i>
                            <strong>Active Shift:</strong> Processing deposit for your current shift.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="deposit_provider" class="form-label fw-bold mb-2">
                                    Float Provider <span class="text-danger">*</span>
                                </label>
                                <select class="form-control ih-medium ip-gray radius-xs b-light px-15 select2"
                                    id="deposit_provider" name="provider" required>
                                    <option value="" selected disabled>Select Provider</option>
                                    @foreach ($floatAccounts as $account)
                                        @php
                                            $providerModel = \App\Models\FloatProvider::where(
                                                'name',
                                                $account->provider,
                                            )->first();
                                            $displayName = $providerModel
                                                ? $providerModel->display_name
                                                : ucfirst($account->provider);
                                            $providerType = $providerModel ? $providerModel->type : 'mobile_money';
                                        @endphp
                                        <option value="{{ $account->provider }}"
                                            data-provider-type="{{ $providerType }}">
                                            {{ $displayName }} (Balance:
                                            {{ formatCurrency(abs($account->balance), 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="deposit_amount" class="form-label fw-bold mb-2">
                                    Amount (TZS) <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control amount ih-medium ip-gray radius-xs b-light px-15"
                                    id="deposit_amount" name="amount" required min="1" step="1"
                                    placeholder="Enter deposit amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="deposit_customer_name" class="form-label fw-bold mb-2">
                                    Customer Name
                                </label>
                                <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                    id="deposit_customer_name" name="customer_name" placeholder="Enter customer name">
                            </div>

                            <div class="col-md-6">
                                <label for="deposit_reference" class="form-label fw-bold mb-2">
                                    Reference (Transaction ID)
                                </label>
                                <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                    id="deposit_reference" name="reference" placeholder="Enter transaction reference">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6" id="deposit_customer_phone_field">
                                <label for="deposit_customer_phone" class="form-label fw-bold mb-2">
                                    Customer Phone <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                    id="deposit_customer_phone" name="customer_phone"
                                    placeholder="Enter customer phone number">
                            </div>

                            <div class="col-md-6" id="deposit_account_no_field" style="display: none;">
                                <label for="deposit_account_no" class="form-label fw-bold mb-2">
                                    Account Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                    id="deposit_account_no" name="account_no" placeholder="Enter account number">
                            </div>
                        </div>

                        <script>
                            // Make function globally accessible
                            window.toggleDepositFields = function() {
                                const providerSelect = document.getElementById('deposit_provider');
                                const phoneField = document.getElementById('deposit_customer_phone_field');
                                const accountField = document.getElementById('deposit_account_no_field');
                                const phoneInput = document.getElementById('deposit_customer_phone');
                                const accountInput = document.getElementById('deposit_account_no');

                                if (!providerSelect || !phoneField || !accountField) return;

                                // Get selected value (works with both regular select and Select2)
                                const selectedValue = providerSelect.value;

                                if (!selectedValue) {
                                    // No provider selected, show phone field by default
                                    phoneField.style.display = 'block';
                                    accountField.style.display = 'none';
                                    return;
                                }

                                // Find the option element with this value
                                const selectedOption = providerSelect.querySelector('option[value="' + selectedValue + '"]');
                                const providerType = selectedOption ? selectedOption.getAttribute('data-provider-type') : null;
                                const isBank = providerType === 'bank';

                                if (isBank) {
                                    phoneField.style.display = 'none';
                                    accountField.style.display = 'block';
                                    phoneInput.removeAttribute('required');
                                    phoneInput.value = '';
                                    accountInput.setAttribute('required', 'required');
                                } else {
                                    phoneField.style.display = 'block';
                                    accountField.style.display = 'none';
                                    accountInput.removeAttribute('required');
                                    accountInput.value = '';
                                    phoneInput.setAttribute('required', 'required');
                                }
                            };
                        </script>
                    @endif
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>Cancel
                    </button>
                    @if ($shift)
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="las la-arrow-up me-1"></i>Process Deposit
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
