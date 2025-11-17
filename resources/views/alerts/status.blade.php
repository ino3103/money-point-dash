@if ($message = Session::get('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="alert-content">
            <p><strong>Success</strong> {{ $message }}</p>
                <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert" aria-label="Close">
                    <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg" aria-hidden="true">
                </button>
        </div>
    </div>
@endif
