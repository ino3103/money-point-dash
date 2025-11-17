@extends('layouts.app')

@section('content')
    <div class="contents">
        <div class="crm mb-25">
            <div class="container-fluid">
                <div class="row">
                    @include('layouts.breadcumb')
                </div>

                <div class="row">
                    @include('alerts.success')
                    @include('alerts.errors')
                    @include('alerts.error')

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ $data['title'] }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('money-point.transactions.withdraw.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="provider" class="form-label">Float Provider <span class="text-danger">*</span></label>
                                            <select class="form-select" id="provider" name="provider" required>
                                                <option value="">Select Provider</option>
                                                @foreach($floatAccounts as $account)
                                                    <option value="{{ $account->provider }}">{{ ucfirst($account->provider) }} (Balance: {{ formatCurrency(abs($account->balance), 0) }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="amount" class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="amount" name="amount" required min="1" step="1">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_phone" class="form-label">Customer Phone</label>
                                            <input type="text" class="form-control" id="customer_phone" name="customer_phone">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="reference" class="form-label">Reference (Transaction ID)</label>
                                            <input type="text" class="form-control" id="reference" name="reference">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-danger">Process Withdrawal</button>
                                            <a href="{{ route('money-point.transactions') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

