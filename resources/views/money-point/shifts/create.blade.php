@extends('layouts.app')

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="contact-list-wrap mb-25">

                            <div class="d-flex justify-content-between mb-2">
                                <div class="action-btn">
                                    <h4 class="text-capitalize fw-500 breadcrumb-title">{{ $data['title'] }}</h4>
                                </div>

                                <div class="action-btn">
                                    <div class="drawer-btn d-flex justify-content-center">
                                        <a href="{{ route('money-point.shifts') }}"
                                            class="btn btn-primary btn-sm btn-default btn-squared" data-drawer="account">
                                            Back
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="contact-list radius-xl w-100">
                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')

                                <form action="{{ route('money-point.shifts.store') }}" method="POST" id="shiftForm">
                                    @csrf

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="teller_id" class="form-label fw-bold mb-2">
                                                Teller <span class="text-danger">*</span>
                                            </label>
                                            <select name="teller_id" id="teller_id"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15 select2 @error('teller_id') is-invalid @enderror">
                                                <option value="" selected disabled>Select Teller</option>
                                                @foreach ($tellers as $teller)
                                                    <option value="{{ $teller->id }}"
                                                        data-previous-cash="{{ isset($previousClosingBalances[$teller->id]) ? $previousClosingBalances[$teller->id]['cash'] : 0 }}"
                                                        data-previous-floats="{{ isset($previousClosingBalances[$teller->id]) ? e(json_encode($previousClosingBalances[$teller->id]['floats'])) : '{}' }}"
                                                        {{ old('teller_id') == $teller->id ? 'selected' : '' }}>
                                                        {{ $teller->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('teller_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="opening_cash" class="form-label fw-bold mb-2">
                                                New Cash Allocation (TZS) <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="opening_cash" id="opening_cash"
                                                class="form-control amount ih-medium ip-gray radius-xs b-light px-15 @error('opening_cash') is-invalid @enderror"
                                                placeholder="Enter new cash amount to allocate"
                                                value="{{ old('opening_cash') }}">
                                            <div class="form-check mt-2">
                                                <input type="hidden" name="use_previous_cash" value="0">
                                                <input class="form-check-input" type="checkbox" id="use_previous_cash"
                                                    name="use_previous_cash" value="1">
                                                <label class="form-check-label" for="use_previous_cash"
                                                    style="font-size: 0.9rem;">
                                                    <strong>✓ Use Previous Closing Balance as Starting Point</strong>
                                                    <br>
                                                    <small class="text-muted">If checked: Account starts from previous
                                                        closing balance, then adds the new allocation above.</small>
                                                    <br>
                                                    <small class="text-muted">If unchecked: Account resets to zero, then
                                                        adds the new allocation above.</small>
                                                </label>
                                            </div>
                                            <small id="previous_cash_info" class="text-muted d-none mt-1 d-block"></small>
                                            @error('opening_cash')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold mb-2">
                                                New Float Allocations (Enter amounts as positive values)
                                            </label>
                                        </div>
                                        @foreach ($floatProviders as $provider)
                                            <div class="col-md-4 mb-3">
                                                <label for="opening_floats_{{ $provider->name }}"
                                                    class="form-label fw-bold mb-2">
                                                    {{ $provider->display_name }}
                                                </label>
                                                <input type="text"
                                                    class="form-control amount ih-medium ip-gray radius-xs b-light px-15"
                                                    id="opening_floats_{{ $provider->name }}"
                                                    name="opening_floats[{{ $provider->name }}]"
                                                    placeholder="Enter new {{ $provider->display_name }} allocation"
                                                    value="{{ old('opening_floats.' . $provider->name, '0') }}">
                                                <div class="form-check mt-2">
                                                    <input type="hidden" name="use_previous_float[{{ $provider->name }}]"
                                                        value="0">
                                                    <input class="form-check-input use-previous-float" type="checkbox"
                                                        id="use_previous_float_{{ $provider->name }}"
                                                        name="use_previous_float[{{ $provider->name }}]" value="1"
                                                        data-provider="{{ $provider->name }}">
                                                    <label class="form-check-label"
                                                        for="use_previous_float_{{ $provider->name }}"
                                                        style="font-size: 0.9rem;">
                                                        <strong>✓ Use Previous Closing Balance</strong>
                                                        <br>
                                                        <small class="text-muted">Start from previous closing, then add
                                                            allocation above.</small>
                                                    </label>
                                                </div>
                                                <small id="previous_float_info_{{ $provider->name }}"
                                                    class="text-muted d-none mt-1 d-block"></small>
                                                @if ($provider->description)
                                                    <small
                                                        class="text-muted d-block mt-1">{{ $provider->description }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="layout-button">
                                        <button type="button" id="cancelBtn"
                                            class="btn btn-default btn-sm btn-squared btn-light px-20">Cancel</button>
                                        <button type="button" id="confirmBtn"
                                            class="btn btn-primary btn-sm btn-default btn-squared px-30">Open Shift</button>
                                    </div>
                                </form>

                                <!-- Confirmation Modal -->
                                <div class="modal fade" id="confirmShiftModal" tabindex="-1"
                                    aria-labelledby="confirmShiftModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title fw-bold" id="confirmShiftModalLabel">
                                                    <i class="las la-check-circle me-2 text-primary"></i>Confirm Shift
                                                    Opening
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="alert alert-warning border-0 mb-4">
                                                    <i class="las la-exclamation-triangle me-2"></i>
                                                    <strong>Please Review:</strong> Once confirmed, this shift cannot be
                                                    edited. Please verify all amounts before proceeding.
                                                </div>

                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body">
                                                        <h6 class="card-title fw-bold mb-3 text-primary">
                                                            <i class="las la-user me-2"></i>Shift Details
                                                        </h6>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label
                                                                    class="form-label text-muted mb-1 small">Teller</label>
                                                                <div class="fw-bold" id="confirmTellerName">-</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label text-muted mb-1 small">Opening
                                                                    Cash</label>
                                                                <div class="fw-bold text-primary" id="confirmOpeningCash">
                                                                    -
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label class="form-label text-muted mb-2 small">Opening
                                                                    Floats</label>
                                                                <div id="confirmOpeningFloats">
                                                                    <p class="text-muted mb-0">No floats entered</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-3 pt-3 border-top">
                                                            <div class="col-12">
                                                                <label class="form-label text-muted mb-1 small">Mtaji
                                                                    (Total Opening Capital)</label>
                                                                <div class="fs-4 fw-bold text-success" id="confirmMtaji">-
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
                                                <button type="button" id="finalConfirmBtn"
                                                    class="btn btn-primary fw-bold">
                                                    <i class="las la-check-circle me-1"></i>Confirm & Open Shift
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 without placeholder alert
            $('#teller_id').select2({
                placeholder: {
                    id: '',
                    text: 'Select Teller'
                },
                allowClear: false,
                dropdownParent: $('#teller_id').parent()
            });

            // Store previous balances
            var previousBalances = {};

            // When teller is selected, load previous closing balances
            $('#teller_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                // Use attr() instead of data() to get the raw attribute value
                var previousCash = selectedOption.attr('data-previous-cash') || 0;
                var previousFloatsStr = selectedOption.attr('data-previous-floats') || '{}';

                // Decode HTML entities (like &quot; to ")
                // Create a textarea element to decode HTML entities
                var textarea = document.createElement('textarea');
                textarea.innerHTML = previousFloatsStr;
                previousFloatsStr = textarea.value;

                // Convert to numbers
                previousCash = parseInt(previousCash) || 0;

                try {
                    var parsedFloats = JSON.parse(previousFloatsStr);
                    // Convert float values to numbers
                    for (var key in parsedFloats) {
                        if (parsedFloats.hasOwnProperty(key)) {
                            parsedFloats[key] = parseInt(parsedFloats[key]) || 0;
                        }
                    }
                    previousBalances = {
                        cash: previousCash,
                        floats: parsedFloats
                    };
                    console.log('Previous balances loaded:', previousBalances);
                } catch (e) {
                    console.error('Error parsing previous floats:', e, 'String:', previousFloatsStr);
                    previousBalances = {
                        cash: previousCash,
                        floats: {}
                    };
                }

                // Show/hide previous cash info
                if (previousBalances.cash && previousBalances.cash > 0) {
                    $('#previous_cash_info').removeClass('d-none').html(
                        '<i class="las la-info-circle"></i> Previous closing cash: <strong>' +
                        formatCurrency(previousBalances.cash) + '</strong>');
                } else {
                    $('#previous_cash_info').addClass('d-none');
                    $('#use_previous_cash').prop('checked', false);
                }

                // Show/hide previous float info for each provider
                @foreach ($floatProviders as $provider)
                    var providerName = '{{ $provider->name }}';
                    var prevFloat = parseInt(previousBalances.floats[providerName] || 0);
                    var infoElement = $('#previous_float_info_' + providerName);

                    if (prevFloat > 0) {
                        if (infoElement.length > 0) {
                            infoElement.removeClass('d-none')
                                .html('<i class="las la-info-circle"></i> Previous closing: <strong>' +
                                    formatCurrency(prevFloat) + '</strong>');
                        }
                    } else {
                        if (infoElement.length > 0) {
                            infoElement.addClass('d-none');
                        }
                        $('#use_previous_float_' + providerName).prop('checked', false);
                    }
                @endforeach
            });

            // Handle "Use Previous" checkbox for cash
            // Note: The checkbox doesn't auto-fill - user must always enter the NEW allocation amount
            // The checkbox only determines if we start from previous balance or zero
            $('#use_previous_cash').on('change', function() {
                // Just update the info display, don't auto-fill the field
                // User should always enter the new allocation amount manually
            });

            // Handle "Use Previous" checkboxes for floats
            // Note: The checkbox doesn't auto-fill - user must always enter the NEW allocation amount
            // The checkbox only determines if we start from previous balance or zero
            $('.use-previous-float').on('change', function() {
                // Just update the info display, don't auto-fill the field
                // User should always enter the new allocation amount manually
            });

            // Cancel button
            $('#cancelBtn').on('click', function() {
                window.location.href = '{{ route('money-point.shifts') }}';
            });

            // Format currency helper
            function formatCurrency(amount) {
                if (!amount || amount === '0' || amount === '') return 'TZS 0';
                var num = parseInt(amount.toString().replace(/[^\d]/g, '')) || 0;
                return 'TZS ' + num.toLocaleString('en-US');
            }

            // Clean amount helper
            function cleanAmount(amount) {
                if (!amount || amount === '') return 0;
                return parseInt(amount.toString().replace(/[^\d]/g, '')) || 0;
            }

            // Confirm button click
            $('#confirmBtn').on('click', function(e) {
                e.preventDefault();

                // Validate form
                var tellerId = $('#teller_id').val();
                var openingCash = $('#opening_cash').val();

                if (!tellerId) {
                    alert('Please select a teller');
                    $('#teller_id').focus();
                    return;
                }

                if (!openingCash || cleanAmount(openingCash) === 0) {
                    alert('Please enter opening cash amount');
                    $('#opening_cash').focus();
                    return;
                }

                // Get teller name
                var tellerName = $('#teller_id option:selected').text();
                var openingCashAmount = cleanAmount(openingCash);

                // Check if using previous balances
                var usePreviousCash = $('#use_previous_cash').is(':checked');
                var usePreviousFloats = {};
                @foreach ($floatProviders as $provider)
                    usePreviousFloats['{{ $provider->name }}'] = $(
                        '#use_previous_float_{{ $provider->name }}').is(':checked');
                @endforeach

                // Get previous balances (ensure they're numbers)
                var previousCash = parseInt(previousBalances.cash || 0);
                var previousFloats = previousBalances.floats || {};

                // Calculate actual starting balances
                var startingCash = usePreviousCash && previousCash > 0 ? (previousCash +
                    openingCashAmount) : openingCashAmount;
                var floats = []; // Initialize floats array
                var totalFloats = 0;
                var totalStartingFloats = 0;

                @foreach ($floatProviders as $provider)
                    var floatAmount = cleanAmount($('#opening_floats_{{ $provider->name }}').val());
                    var usePrevious = usePreviousFloats['{{ $provider->name }}'] === true;
                    var previousFloat = parseInt(previousFloats['{{ $provider->name }}'] || 0);
                    var startingFloat = (usePrevious && previousFloat > 0) ? (previousFloat + floatAmount) :
                        floatAmount;

                    // Always include float if there's an amount or if using previous
                    if (floatAmount > 0 || (usePrevious && previousFloat > 0)) {
                        floats.push({
                            name: '{{ $provider->display_name }}',
                            newAmount: floatAmount,
                            previousAmount: previousFloat,
                            startingAmount: startingFloat,
                            usePrevious: usePrevious && previousFloat > 0
                        });
                        totalFloats += floatAmount;
                        totalStartingFloats += startingFloat;
                    }
                @endforeach

                // Calculate Mtaji (total starting capital)
                var mtaji = startingCash + totalStartingFloats;

                // Populate confirmation modal
                $('#confirmTellerName').text(tellerName);

                // Show cash details
                var cashHtml = '<div class="mb-2">';
                cashHtml += '<div class="d-flex justify-content-between align-items-center mb-1">';
                cashHtml += '<span class="text-muted">New Allocation:</span>';
                cashHtml += '<strong>' + formatCurrency(openingCashAmount) + '</strong>';
                cashHtml += '</div>';
                if (usePreviousCash && previousCash > 0) {
                    cashHtml +=
                        '<div class="d-flex justify-content-between align-items-center mb-1 text-success">';
                    cashHtml += '<span>+ Previous Closing Balance:</span>';
                    cashHtml += '<strong>' + formatCurrency(previousCash) + '</strong>';
                    cashHtml += '</div>';
                    cashHtml +=
                        '<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">';
                    cashHtml += '<span class="fw-bold">Starting Cash (Previous + New):</span>';
                    cashHtml += '<strong class="text-primary fs-5">' + formatCurrency(startingCash) +
                        '</strong>';
                    cashHtml += '</div>';
                } else {
                    cashHtml +=
                        '<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">';
                    cashHtml += '<span class="fw-bold">Starting Cash (New Allocation Only):</span>';
                    cashHtml += '<strong class="text-primary fs-5">' + formatCurrency(startingCash) +
                        '</strong>';
                    cashHtml += '</div>';
                }
                cashHtml += '</div>';
                $('#confirmOpeningCash').html(cashHtml);

                // Show float details
                var floatsHtml = '';
                if (floats.length > 0) {
                    floats.forEach(function(float) {
                        floatsHtml += '<div class="mb-3 pb-3 border-bottom">';
                        floatsHtml += '<div class="fw-bold mb-2">' + float.name + '</div>';
                        floatsHtml +=
                            '<div class="d-flex justify-content-between align-items-center mb-1">';
                        floatsHtml += '<span class="text-muted">New Allocation:</span>';
                        floatsHtml += '<strong>' + formatCurrency(float.newAmount) + '</strong>';
                        floatsHtml += '</div>';
                        if (float.usePrevious && float.previousAmount > 0) {
                            floatsHtml +=
                                '<div class="d-flex justify-content-between align-items-center mb-1 text-success">';
                            floatsHtml += '<span>+ Previous Closing Balance:</span>';
                            floatsHtml += '<strong>' + formatCurrency(float.previousAmount) +
                                '</strong>';
                            floatsHtml += '</div>';
                            floatsHtml +=
                                '<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">';
                            floatsHtml +=
                                '<span class="fw-bold">Starting Balance (Previous + New):</span>';
                            floatsHtml += '<strong class="text-success fs-6">' + formatCurrency(
                                float
                                .startingAmount) + '</strong>';
                            floatsHtml += '</div>';
                        } else {
                            floatsHtml +=
                                '<div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">';
                            floatsHtml +=
                                '<span class="fw-bold">Starting Balance (New Allocation Only):</span>';
                            floatsHtml += '<strong class="text-success fs-6">' + formatCurrency(
                                float
                                .startingAmount) + '</strong>';
                            floatsHtml += '</div>';
                        }
                        floatsHtml += '</div>';
                    });
                } else {
                    floatsHtml = '<p class="text-muted mb-0">No floats entered</p>';
                }
                $('#confirmOpeningFloats').html(floatsHtml);
                $('#confirmMtaji').text(formatCurrency(mtaji));

                // Show modal
                $('#confirmShiftModal').modal('show');
            });

            // Final confirm button
            $('#finalConfirmBtn').on('click', function() {
                // Submit the form
                $('#shiftForm').submit();
            });
        });
    </script>
@endpush
