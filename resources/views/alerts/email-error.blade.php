@error('email')
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="alert-content">
            <p>{{ $message }}</p>
            <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert" aria-label="Close">
                <img src="{{ asset('assets/img/svg/x.svg') }}" alt="x" class="svg" aria-hidden="true">
            </button>
        </div>
    </div>
@enderror
