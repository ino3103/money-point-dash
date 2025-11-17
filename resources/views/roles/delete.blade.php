<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="{{ route('roles.destroy') }}" method="post">
                <div class="modal-body">
                    <div class="modal-info-body d-flex">
                        <div class="modal-info-icon warning">
                            <img src="{{ asset('assets/img/svg/alert-circle.svg') }}" alt="alert-circle" class="svg">
                        </div>
                        <div class="modal-info-text">
                            @csrf
                            @method('DELETE')
                            <h6>Do you want to delete Role <span id="nameToDelete"></span>?</h6>
                            <p>This action cannot be undone.</p>
                            <input type="hidden" name="id" id="id" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-outlined btn-sm"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
