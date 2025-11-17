<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.update') }}" id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="setting-id">
                    <div class="mb-3">
                        <label for="setting-key" class="form-label">Key <span style="color: red">*</span></label>
                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="setting-key" name="key" readonly>
                    </div>
                    <div class="mb-3" id="value-input-container">

                    </div>
                    <div class="mb-3">
                        <label for="setting-description" class="form-label">Description <span
                                style="color: red">*</span></label>
                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="setting-description" name="description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
