<div id="deleteUserModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteUserForm">
                <div class="modal-header">
                    <h3>Delete User</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: bold;">Email</label>
                        <div id="deleteEmail" class="form-control-plaintext">
                            <!-- This will be populated with the email value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Would you like to delete this user?</label>
                        <select type="deleteUser" name="deleteUser" id="deleteUser" class="form-control" required>
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select><br>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Would you like to delete all of their associated blogs?</label>
                        <select type="deleteUserBlogs" name="deleteUserBlogs" id="deleteUserBlogs" class="form-control" required>
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveDeleteUserChanges()">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>