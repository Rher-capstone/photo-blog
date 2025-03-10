<div id="editUserModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm">
                <div class="modal-header">
                    <h3>Edit User</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: bold;">Email</label>
                        <div id="editEmail" class="form-control-plaintext">
                            <!-- This will be populated with the email value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">First Name</label>
                        <input type="firstName" name="firstName" id="editFirstName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Last Name</label>
                        <input type="lastName" name="lastName" id="editLastName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Password</label>
                        <input type="password" name="password" id="editPassword" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Active</label>
                        <select type="active" name="active" id="editActive" class="form-control" required>
                            <option value="1">Active</option>
                            <option value="0">Not Active</option>
                        </select><br>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" for="role">Role</label>
                        <select type="role" name="role" id="editRole" class="form-control" required>
                            <option value="blogger">Blogger</option>
                            <option value="admin">Admin</option>
                        </select><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveUserChanges()">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
