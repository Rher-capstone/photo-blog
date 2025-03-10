<body>
    <div class="profile-section">
        <div class="make-row">
            <div class="profile-contents">
                <div class="section-contents">
                    <h5> Profile Photo: </h5>
                    <div class="settings-profile-photo">
                        <!-- Display the user's profile photo -->
                        <img src="<?= isset($_SESSION['user_img']) ? $_SESSION['user_img'] : '/images/blankicon.jpg' ?>" alt="userImage">
                    </div>
                </div>
                <button class="settings-button" id="edit-profile-photo" data-bs-toggle="modal" data-bs-target="#edit-profile-photo-modal">Edit Profile Photo</button>
            </div>
            <div class="make-column">
                <div class="profile-contents">
                    <div class="section-contents">
                        <h5> My First Name: </h5>
                        <h6> <?php echo $_SESSION['current_user_first_name']; ?> <h6>
                    </div>
                    <button class="settings-button" id="edit-first-name" data-bs-toggle="modal" data-bs-target="#edit-first-name-modal">Edit First Name</button>
                </div>
                <div class="profile-contents">
                    <div class="section-contents">
                        <h5> My Last Name: </h5>
                        <h6> <?php echo $_SESSION['current_user_last_name']; ?> <h6>
                    </div>
                    <button class="settings-button" id="edit-last-name" data-bs-toggle="modal" data-bs-target="#edit-last-name-modal">Edit Last Name</button>
                </div>
            </div>
        </div>
        <div class="profile-password">
            <h5> Password: </h5>
            <button class="settings-button" id="edit-password" data-bs-toggle="modal" data-bs-target="#edit-password-modal">Change Password</button>
        </div>
    </div>

    <script>
        const editProfilePhotoButton = document.getElementById('edit-profile-photo');
        const editFirstNameButton = document.getElementById('edit-first-name');
        const editLastNameButton = document.getElementById('edit-last-name');
        const editPasswordButton = document.getElementById('edit-password');

        editProfilePhotoButton.addEventListener('click', () => {
            editProfilePhotoModal.style.display = 'block';
        });
        
        editFirstNameButton.addEventListener('click', () => {
            editFirstNameModal.style.display = 'block';
        });
        
        editLastNameButton.addEventListener('click', () => {
            editLastNameModal.style.display = 'block';
        });

        editPasswordButton.addEventListener('click', () => {
            editPasswordModal.style.display = 'block';
        });

        function saveProfilePhotoChange(event) {
            // Prevent form submission
            event.preventDefault();

            const formData = new FormData();
            const fileInput = document.getElementById('profile-photo');

            if (fileInput.files.length === 0) {
                alert('Please select a file');
                return;
            }

            formData.append('profile-photo', fileInput.files[0]);

            fetch('actions/update-profile/update-profile-photo.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile photo updated successfully!');
                    document.querySelector('.settings-profile-photo img').src = data.filePath;
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function saveFirstNameChange(event) {
            // Prevent form submission
            event.preventDefault();

            const formData = {
                firstName: document.getElementById('first-name').value.trim()
            };

            fetch('actions/update-profile/update-first-name.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('First name updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating name: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function saveLastNameChange(event) {
            // Prevent form submission
            event.preventDefault();

            const formData = {
                lastName: document.getElementById('last-name').value.trim()
            };

            fetch('actions/update-profile/update-last-name.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Last name updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating name: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function savePasswordChange(event) {
            // Prevent form submission
            event.preventDefault();

            const formData = {
                password: document.getElementById('password').value.trim()
            };

            fetch('actions/update-profile/update-password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating password: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
