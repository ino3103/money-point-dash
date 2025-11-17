<div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('profile.password.update') }}" id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePasswordModalLabel">Update Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password <span style="color: red">*</span></label>
                        <input type="password" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="current_password" name="current_password" value="">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span style="color: red">*</span></label>
                        <input type="password" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="new_password" name="new_password" value="">
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password<span style="color: red">*</span></label>
                        <input type="password" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="new_password_confirmation" name="new_password_confirmation" value="">
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
