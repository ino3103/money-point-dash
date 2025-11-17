<div class="modal fade" id="sendMailModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('test-email.send') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Send Test Mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span style="color: red">*</span></label>
                        <input type="email" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="email" name="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Send Test Email</button>
                </div>
            </form>
        </div>
    </div>
</div>
