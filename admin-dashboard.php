<?php
session_start();
$CURRENT_PAGE = "Administration";
include_once("includes/db-conn.php");

$base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/photo_abcd_A/';

if (!isset($_SESSION['current_user_email']) || !isset($_SESSION['current_user_role']) || $_SESSION['current_user_role'] !== 'admin') {
    header('Location: ' . $base_url . 'index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include("includes/head-tag-contents.php");?>
		<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" />
		<link href="css/tables.css" rel="stylesheet" type="text/css">
    </head>
	<body>
		<?php include("includes/top-bar.php");?>
		<h2>Administration</h2>
			<div class="container" id='main-body'>
				<section>
					<?php
						// Check connection
						if ($conn->connect_error) {
							die("Connection failed: " . $conn->connect_error);
						}

						$sql = "SELECT email, first_name, last_name, password, active, role, created_time, modified_time FROM users";
						$result = $conn->query($sql);
					?>
					<div class="tableContainer">
						<h3>Users</h3>
						<table id="usersTable" class="styledTable">
							<thead>
								<tr class="header">
									<th>Email</th>
									<th>First Name</th>
									<th>Last Name</th>
									<th>Password</th>
									<th>Active</th>
									<th>Role</th>
									<th>Created Time</th>
									<th>Modified Time</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									// Function to format the date and time
									function formatDateTime($dateString) {
										try {
											$date = new DateTime($dateString);
											$datetimeLocalFormat = $date->format('Y-m-d\TH:i');
											return $datetimeLocalFormat;
										} catch (Exception $e) {
											return "Invalid Date";
										}
									}

									// Function to format active value into a string value
									function formatActive($activeValue) {
										try {
											if ($activeValue == "0") {
												return "Not Active";
											} else if ($activeValue == "1") {
												return "Active";
											}
										} catch (Exception $e) {
											return "Invalid Active Value";
										}
									}

									while($row = $result->fetch_assoc()) {
								?>
										<tr>
											<td><?php echo $row['email']; ?></td>
											<td><?php echo $row['first_name']; ?></td>
											<td><?php echo $row['last_name']; ?></td>
											<td><?php echo $row['password']; ?></td>
											<td><?php echo formatActive($row['active']); ?></td>
											<td><?php echo $row['role']; ?></td>
											<td><?php echo formatDateTime($row['created_time']); ?></td>
											<td><?php echo formatDateTime($row['modified_time']); ?></td>
										</tr>
							<?php } ?>
							</tbody>
						</table>
						<button id="editUserButton">Edit User</button>
						<button id="deleteUserButton">Delete User</button>
						<button id="viewAlphabetCountsButton">View User Alphabet Book Counts</button>
						<?php include("includes/edit-user-modal.php");?>
						<?php include("includes/delete-user-modal.php");?>
						<?php include("includes/view-alphabet-counts-modal.php");?>
					</div>
				</section>
				</br>
				<section>
					<?php
						// Fetch blogs
						$sql = "SELECT * FROM blogs";
						$result = $conn->query($sql);
					?>
					<div class="tableContainer">
						<h3>Blogs</h3>
						<table id="blogsTable" class="styledTable">
							<thead>
								<tr class="header">
									<th>ID</th>
									<th>Creator Email</th>
									<th>Title</th>
									<th>Description</th>
									<th>Youtube Link</th>
									<th>Event Date</th>
									<th>Creation Date</th>
									<th>Modification Date</th>
									<th>Privacy Filter</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									while($row = $result->fetch_assoc()) {
								?>
										<tr>
											<td><?php echo $row['blog_id']; ?></td>
											<td><?php echo $row['creator_email']; ?></td>
											<td><?php echo $row['title']; ?></td>
											<td><?php echo $row['description']; ?></td>
											<td><?php echo $row['youtube_link']; ?></td>
											<td><?php echo formatDateTime($row['event_date']); ?></td>
											<td><?php echo formatDateTime($row['creation_date']); ?></td>
											<td><?php echo formatDateTime($row['modification_date']); ?></td>
											<td><?php echo $row['privacy_filter']; ?></td>
										</tr>
								<?php } ?>
							</tbody>
						</table>
						<button id="editBlogButton">Edit Blog</button>
						<button id="deleteBlogButton">Delete Blog</button>
						<?php include("includes/edit-blog-modal.php");?>
						<?php include("includes/delete-blog-modal.php");?>
					</div>
				</section>
				</br>
				<section>
					<?php
						// Fetch user alphabet book counts
						$sql = "
							SELECT 
								u.email AS creator_email,
								COALESCE(SUM(CASE WHEN LetterCount > 0 THEN 1 ELSE 0 END), 0) AS LettersWithCount,
								(26 - COALESCE(SUM(CASE WHEN LetterCount > 0 THEN 1 ELSE 0 END), 0)) AS LettersWithoutCount
							FROM users u
							LEFT JOIN (
								SELECT 
									creator_email,
									COUNT(*) AS LetterCount 
								FROM blogs
								GROUP BY creator_email, LEFT(title, 1)
							) AS LetterCounts ON u.email = LetterCounts.creator_email
							GROUP BY u.email
						";
						$result = $conn->query($sql);
					?>
					<div class="tableContainer">
						<h3>Alphabet Book Counts</h3>
						<table id="adminAlphabetBookCountsTable" class="styledTable">
							<thead>
								<tr class="header">
									<th>User Email</th>
									<th>Completed</th>
									<th>Pending</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									while($row = $result->fetch_assoc()) {
								?>
									<tr>
										<td><?php echo $row['creator_email']; ?></td>
										<td><?php echo $row['LettersWithCount']; ?></td>
										<td><?php echo $row['LettersWithoutCount']; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</section>
				</br>
				<section>
					<?php
						$sql1 = "SELECT count(*) as total_users FROM users";
						$result1 = $conn->query($sql1);

						$sql2 = "SELECT count(*) as total_blogs FROM blogs";
						$result2 = $conn->query($sql2);

						if ($result1) {
							$row1 = $result1->fetch_assoc();
							$totalUsers = $row1['total_users'];
						} else {
							$totalUsers = "Error fetching count";
						}
				
						if ($result2) {
							$row2 = $result2->fetch_assoc();
							$totalBlogs = $row2['total_blogs'];
						} else {
							$totalBlogs = "Error fetching count";
						}
					?>
					<div class="tableContainer">
						<h3>Site Totals</h3>
						<table id="siteTotalsTable" class="styledTable">
							<thead>
								<tr class="header">
									<th>Count Type</th>
									<th>Total Count</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Total Number of Users</td>
									<td><?php echo $totalUsers; ?></td>
								</tr>
								<tr>
									<td>Total Number of Blog Entries</td>
									<td><?php echo $totalBlogs; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</section>
				</br>
				<section>
				<?php
					// Query to fetch the blog mode from the preferences table for the 'BLOG_MODE' setting
					$sql1 = "SELECT value FROM preferences WHERE name = 'BLOG_MODE'"; 
					$result = $conn->query($sql1);

					// Fetch the result and get the blog_mode value
					if ($result->num_rows > 0) {
						// Assuming 'value' is the column storing the blog mode value
						$row = $result->fetch_assoc();
						$blogMode = $row['value']; 
					} else {
						$blogMode = "No mode set"; // Default if no data found
					}
				?>
                    <div class="tableContainer">
                        <h3>Site Blog Modes</h3>
						<p1>Current Site Blog Mode: <?php echo htmlspecialchars($blogMode); ?> </p1>
                        <table id="siteBlogModeTable" class="styledTable">
                            <thead>
                                <tr class="header">
                                    <th>Site Blog Mode Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Photos</td>
                                </tr>
                                <tr>
                                    <td>Mixed</td>
                                </tr>
                                <tr>
                                    <td>Videos</td>
                                </tr>
                            </tbody>
                        </table>
                        <button id="updateSiteBlogModeButton">Update Site Blog Mode</button>
                    </div>
                </section>
			</div>

			<?php include("includes/footer.php");?>
			<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

			
			<script>
			function saveUserChanges() {
				const formData = {
					email: document.getElementById('editEmail').innerText,
					firstName: document.getElementById('editFirstName').value,
					lastName: document.getElementById('editLastName').value,
					password: document.getElementById('editPassword').value,
					active: document.getElementById('editActive').value,
					role: document.getElementById('editRole').value
				};

				// AJAX request
				fetch('actions/update-user.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(formData),
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('User updated successfully!');
						location.reload();
					} else {
						alert('Error updating user: ' + data.message);
					}
				})
				.catch((error) => {
					console.error('Error:', error);
				});
			}

			function saveBlogChanges() {
				const formData = {
					blogId: document.getElementById('editBlogId').innerText,
					creatorEmail: document.getElementById('editCreatorEmail').innerText,
					title: document.getElementById('editTitle').value,
					description: document.getElementById('editDescription').value,
					youtubeLink: document.getElementById('editYoutubeLink').value,
					eventDate: document.getElementById('editEventDate').value,
					creationDate: document.getElementById('editCreationDate').value,
					modificationDate: document.getElementById('editModificationDate').value,
					privacyFilter: document.getElementById('editPrivacyFilter').value
				};

				// AJAX request
				fetch('actions/update-blog.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(formData),
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('Blog updated successfully!');
						location.reload();
					} else {
						alert('Error updating blog: ' + data.message);
					}
				})
				.catch((error) => {
					console.error('Error:', error);
				});
			}

			function saveDeleteBlogChanges() {
				const formData = {
					blogId: document.getElementById('deleteBlogId').innerText,
					creatorEmail: document.getElementById('deleteCreatorEmail').innerText,
					title: document.getElementById('deleteTitle').innerText,
					description: document.getElementById('deleteDescription').innerText,
					deleteBlog: document.getElementById('deleteBlog').value,
				};

				// Exit if both deleteUser is "no"
				if (formData.deleteBlog === 'no') {
					$('#deleteBlogModal').modal('hide');
					return;
				}

				// AJAX request
				fetch('actions/delete-blog.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(formData),
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('Blog deleted successfully!');
						location.reload();
					} else {
						alert('Error deleting blog: ' + data.message);
					}
				})
				.catch((error) => {
					console.error('Error:', error);
				});
			}

			function saveDeleteUserChanges() {
				const formData = {
					email: document.getElementById('deleteEmail').innerText,
					deleteUser: document.getElementById('deleteUser').value,
					deleteUserBlogs: document.getElementById('deleteUserBlogs').value
				};

				// Exit if both deleteUser is "no"
				if (formData.deleteUser === 'no') {
					$('#deleteUserModal').modal('hide');
					return;
				}

				// AJAX request
				fetch('actions/delete-user.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(formData),
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('User deleted successfully!');
						location.reload();
					} else {
						alert('Error deleting user: ' + data.message);
					}
				})
				.catch((error) => {
					console.error('Error:', error);
				});
			}

			function showAlphabetCountsModal(email) {
				// Clear the table
				$('#alphabetCountsTable tbody').empty();

				// Destroy the DataTable instance if it exists
				if ($.fn.dataTable.isDataTable('#alphabetCountsTable')) {
					$('#alphabetCountsTable').DataTable().clear().destroy();
				}

				$.ajax({
					url: 'actions/get-alphabet-counts-for-admin.php',
					type: 'GET',
					data: { email: email },  // Send the email
					dataType: 'json',
					success: function(response) {
						if (Array.isArray(response) && response.length > 0) {
							response.forEach(function(row) {
								$('#alphabetCountsTable tbody').append(
									'<tr><td>' + row.Letter + '</td><td>' + row.LetterCount + '</td></tr>'
								);
							});

							// Re-initialize DataTable after populating the table
							$('#alphabetCountsTable').DataTable();
							$('#viewAlphabetCountsModal').modal('show'); // Show the modal
						} else {
							console.warn('No data returned for the given email.');
							$('#viewAlphabetCountsModal').modal('show'); // Show the modal even if there's no data
						}
					},
					error: function(xhr, status, error) {
						console.error("Error fetching data:", error);
					}
				});
			}

			function exitViewAlphabetCounts() {
				$('#viewAlphabetCountsModal').modal('hide');
			}

			$(document).ready(function() {
				$('#usersTable').DataTable();
				$('#blogsTable').DataTable();
				$('#adminAlphabetBookCountsTable').DataTable();
				$('#siteTotalsTable').DataTable();
				$('#siteBlogModeTable').DataTable();

				const usersTable = new DataTable('#usersTable');
				const blogsTable = new DataTable('#blogsTable');
				const adminAlphabetBookCountsTable = new DataTable('#adminAlphabetBookCountsTable');
				const siteTotalsTable = new DataTable('#siteTotalsTable');
				const siteBlogModeTable = new DataTable('#siteBlogModeTable');

				usersTable.on('click', 'tbody tr', function (e) {
					if ($(this).hasClass('selected')) {
						$(this).removeClass('selected');
					} else {
						$('#usersTable tbody tr').removeClass('selected');
						$(this).addClass('selected');
					}
				});

				// Click listener for the Edit user button
				$('#editUserButton').on('click', function() {
					const selectedRow = $('#usersTable tbody tr.selected');

					if (selectedRow.length === 0) {
						alert('Please select a user to edit.');
						return;
					}

					const email = selectedRow.find('td:eq(0)').text(); // Email
					const firstName = selectedRow.find('td:eq(1)').text(); // First Name
					const lastName = selectedRow.find('td:eq(2)').text(); // Last Name
					const password = selectedRow.find('td:eq(3)').text(); // Password
					const active = selectedRow.find('td:eq(4)').text(); // Active
					const role = selectedRow.find('td:eq(5)').text(); // Role

					// Fill in fields in the modal
					$('#editEmail').text(email);
					$('#editFirstName').val(firstName);
					$('#editLastName').val(lastName);
					$('#editPassword').val(password);
					$('#active').val(active);
					$('#role').val(role);

					$('#editUserModal').modal('show');
				});

				// Click listener for the Delete user button
				$('#deleteUserButton').on('click', function() {
					const selectedRow = $('#usersTable tbody tr.selected');

					if (selectedRow.length === 0) {
						alert('Please select a user to delete.');
						return;
					}

					const email = selectedRow.find('td:eq(0)').text(); // Email

					// Fill in fields in the modal
					$('#deleteEmail').text(email);

					$('#deleteUserModal').modal('show');
				});

				// Click listener for the View Alphabet Counts button
				$('#viewAlphabetCountsButton').on('click', function() {
					const selectedRow = $('#usersTable tbody tr.selected');

					if (selectedRow.length === 0) {
						alert('Please select a user to view alphabet book counts.');
						return;
					}

					const userEmail = selectedRow.find('td:eq(0)').text(); // Email
					const userFirstName = selectedRow.find('td:eq(1)').text(); // First Name
					const userLastName = selectedRow.find('td:eq(2)').text(); // Last Name

					// Fill in fields in the modal
					$('#userFullName').val(' ' + userFirstName + ' ' + userLastName);

					// Call the function to show the modal and load alphabet counts for the selected user
					showAlphabetCountsModal(userEmail);
				});


				blogsTable.on('click', 'tbody tr', function (e) {
					if ($(this).hasClass('selected')) {
						$(this).removeClass('selected');
					} else {
						$('#blogsTable tbody tr').removeClass('selected');
						$(this).addClass('selected');
					}
				});

				// Click listener for the Edit blog button
				$('#editBlogButton').on('click', function() {
					const selectedRow = $('#blogsTable tbody tr.selected');

					if (selectedRow.length === 0) {
						alert('Please select a blog to edit.');
						return;
					}

					const blogId = selectedRow.find('td:eq(0)').text(); // Blog ID
					const creatorEmail = selectedRow.find('td:eq(1)').text(); // Creator Email
					const title = selectedRow.find('td:eq(2)').text(); // Title
					const description = selectedRow.find('td:eq(3)').text(); // Description
					const youtubeLink = selectedRow.find('td:eq(4)').text(); // Youtube Link
					const eventDate = selectedRow.find('td:eq(5)').text(); // Event Date
					const creationDate = selectedRow.find('td:eq(6)').text(); // Creation Date
					const modificationDate = selectedRow.find('td:eq(7)').text(); // Modification Date
					const privacyFilter = selectedRow.find('td:eq(8)').text(); // Privacy Filter

					// Fill in form fields in the modal
					$('#editBlogId').text(blogId);
					$('#editCreatorEmail').text(creatorEmail);
					$('#editTitle').val(title);
					$('#editDescription').val(description);
					$('#editYoutubeLink').val(youtubeLink);
					$('#editEventDate').val(eventDate);
					$('#editCreationDate').val(creationDate);
					$('#editModificationDate').val(modificationDate);
					$('#editPrivacyFilter').val(privacyFilter);

					$('#editBlogModal').modal('show');
				});

				// Click listener for the Delete blog button
				$('#deleteBlogButton').on('click', function() {
					const selectedRow = $('#blogsTable tbody tr.selected');

					if (selectedRow.length === 0) {
						alert('Please select a blog to delete.');
						return;
					}

					const blogId = selectedRow.find('td:eq(0)').text(); // Blog ID
					const creatorEmail = selectedRow.find('td:eq(1)').text(); // Creator Email
					const title = selectedRow.find('td:eq(2)').text(); // Title
					const description = selectedRow.find('td:eq(3)').text(); // Description

					// Fill in form fields in the modal
					$('#deleteBlogId').text(blogId);
					$('#deleteCreatorEmail').text(creatorEmail);
					$('#deleteTitle').text(title);
					$('#deleteDescription').text(description);
				
					$('#deleteBlogModal').modal('show');
				});

				adminAlphabetBookCountsTable.on('click', 'tbody tr', function (e) {
					if ($(this).hasClass('selected')) {
						$(this).removeClass('selected');
					} else {
						$('#adminAlphabetBookCountsTable tbody tr').removeClass('selected');
						$(this).addClass('selected');
					}
				});

				siteTotalsTable.on('click', 'tbody tr', function (e) {
					if ($(this).hasClass('selected')) {
						$(this).removeClass('selected');
					} else {
						$('#siteTotalsTable tbody tr').removeClass('selected');
						$(this).addClass('selected');
					}
				});

				siteBlogModeTable.on('click', 'tbody tr', function (e) {
					if ($(this).hasClass('selected')) {
						$(this).removeClass('selected');
					} else {
						$('#siteBlogModeTable tbody tr').removeClass('selected');
						$(this).addClass('selected');
					}
				});

			});

			function updateSiteBlogMode(siteBlogModeOption) {
				const formData = {
					siteBlogMode: siteBlogModeOption
				};

				// AJAX request
				fetch('actions/update-site-blog-mode.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(formData),
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert(`SUCCESS: The Site Blog Mode has been updated to ${siteBlogModeOption}.`);
						location.reload(); // Reload the page after update
					} else {
						alert(`Error updating Site Blog Mode: ${data.message || 'Unknown error'}`);
					}
				})
				.catch((error) => {
					console.error('Error:', error);
					alert('Error connecting to the server.');
				});
			}

			// Click listener for the Update Blog Mode button
			$('#updateSiteBlogModeButton').on('click', function() {
				const selectedRow = $('#siteBlogModeTable tbody tr.selected');

				if (selectedRow.length === 0) {
					alert('Please select a site blog mode.');
					return;
				}

				const siteBlogMode = selectedRow.find('td:eq(0)').text(); // Selected Site Blog Mode

				// Ensure the value is valid
				if (!siteBlogMode) {
					alert('Invalid blog mode selected.');
					return;
				}

				// Show the confirmation pop-up
				const isConfirmed = window.confirm(`Are you sure you want to update the Site Blog Mode to ${siteBlogMode}?`);

				if (isConfirmed) {
					updateSiteBlogMode(siteBlogMode);
				}
			});


		</script>

	</body>
</html>