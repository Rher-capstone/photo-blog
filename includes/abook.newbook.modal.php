<div class="modal fade" id="new-book-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content" id="abook-modal-content">
            <form id="login-form">
                <div class="modal-header">
                    <h3 id='abook-modal-title'>Create New Book</h3>
                </div>

                <div class="modal-body" id='new-book-modal-body'>

                    <div class="form-group">
                        <label>Book Title</label>
                        <input type="text" name="title" class="form-control" id="newbook-title" required minlength="1" maxlength="16" pattern="[a-zA-Z0-9\s]{1,16}">
                        <span class="error" aria-live="polite" id="newbook-error"></span>
                    </div>

                </div>

                <div class="modal-footer">

                    <div class="row" id="button-container">

                        <div class="col">
                            <button type="button" class="btn btn-primary" id='newbook-confirm-button'>Confirm</button>
                        </div>

                        <div class="col">
                            <button type="button" class="btn btn-danger" id='newbook-cancel-button' data-bs-dismiss="modal">Cancel</button>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>