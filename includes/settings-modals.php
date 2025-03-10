<!-- Edit Profile Photo Modal -->
<div class="modal fade" id="edit-profile-photo-modal" tabindex="-1" aria-labelledby="editProfilePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-profile-photo-form" action="actions/update-profile/update-profile-photo.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfilePhotoModalLabel">Edit Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profile-photo">Select a new profile photo</label>
                        <input type="file" name="profile-photo" id="profile-photo" class="form-control" accept=".jpg, .jpeg, .png" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="saveProfilePhotoChange(event)">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Edit First Name Modal -->
<div class="modal fade" id="edit-first-name-modal" tabindex="-1" aria-labelledby="editFirstNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-first-name-form" action="actions/update-profile/update-first-name.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFirstNameModalLabel">Edit First Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="first_name" id="first-name" class="form-control" placeholder="Enter new first name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="saveFirstNameChange(event)">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Last Name Modal -->
<div class="modal fade" id="edit-last-name-modal" tabindex="-1" aria-labelledby="editLastNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-last-name-form" action="actions/update-profile/update-last-name.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLastNameModalLabel">Edit Last Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="last_name" id="last-name" class="form-control" placeholder="Enter new last name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="saveLastNameChange(event)">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Password Modal -->
<div class="modal fade" id="edit-password-modal" tabindex="-1" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-password-form" action="actions/update-profile/update-password.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="password" id="password" class="form-control" placeholder="Enter new password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="savePasswordChange(event)">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
