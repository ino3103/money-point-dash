<div class="modal fade" id="updateDetailsModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('profile.update') }}" id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="updateDetailsModalLabel">Update Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span style="color: red">*</span></label>
                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="name" name="name" value="{{ Auth::user()->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span style="color: red">*</span></label>
                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="email" name="email" value="{{ Auth::user()->email }}">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">UserName<span style="color: red">*</span></label>
                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="username" name="username" value="{{ Auth::user()->username }}">
                    </div>
                    <div class="mb-3">
                        <label for="phone_no" class="form-label">Phone No <span style="color: red">*</span></label>
                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="phone_no" name="phone_no" value="{{ Auth::user()->phone_no }}">
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
