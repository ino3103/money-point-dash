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
                                <form action="{{ route('money-point.shifts.submit.store', $shift->id) }}" method="POST">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6>Opening Cash: {{ formatCurrency($shift->opening_cash, 0) }}</h6>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="closing_cash" class="form-label">Closing Cash (Actual Count) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="closing_cash" name="closing_cash" required min="0" step="1">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Closing Floats (Enter amounts as positive values from phone)</label>
                                            <div class="row">
                                                @foreach($shift->opening_floats ?? [] as $provider => $amount)
                                                    <div class="col-md-4 mb-3">
                                                        <label for="closing_floats_{{ $provider }}" class="form-label">{{ $providerNames[$provider] ?? ucfirst($provider) }}</label>
                                                        <input type="number" class="form-control" id="closing_floats_{{ $provider }}" name="closing_floats[{{ $provider }}]" min="0" step="1" required>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Submit Shift</button>
                                            <a href="{{ route('money-point.shifts.show', $shift->id) }}" class="btn btn-secondary">Cancel</a>
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

