<div class="container" id="main-content">
    <?php include('includes/sort-util-bar.php')?>

    <div id="photosOnly">
        <label for="P">
    </div>

    <!-- Posts Container for Grid -->
    <div id="postsContainer" class="grid-container"></div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal" style="display: none;">
        <!-- Initially hidden -->
        <div class="modal-content">
            <span id="closeModal" style="cursor: pointer;">&times;</span>
            <h2>Edit Blog Post</h2>

            <input type="hidden" id="blogId"> <!-- Hidden input for blog ID -->

            <label for="title">Title:</label>
            <input type="text" id="editTitle" required placeholder="Enter blog title"><br>

            <label for="description">Description:</label>
            <textarea id="editDescription" required placeholder="Enter blog description"></textarea><br>

            <label for="eventDate">Event Date:</label>
            <input type="date" id="eventDate" required><br>

            <label for="privacyFilter">Privacy Filter:</label>
            <select id="privacyFilter" name="privacyFilter">
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select><br>

            <input type="hidden" id="creatorEmail"> <!-- Hidden input for creator email -->
            <input type="hidden" id="creationDate"> <!-- Hidden input for creation date -->

            <div id="photoGallery">
                <button id="prevPhoto" class="photo-nav-button" style="display:none;">&#10094;</button>
                <div class="modal-photo-container">
                    <img id="photoDisplay" src="" alt="Blog Photo" class="photo-display" />
                    <button id="deletePhotoButton" class="delete-photo-button" title="Delete Photo">&times;</button>
                </div>
                <button id="nextPhoto" class="photo-nav-button">&#10095;</button>
            </div>

            <label for="photoUpload">Upload Photo:</label>
            <input type="file" id="photoUpload" name="photos[]" accept=".jpg, .jpeg, .png, .gif" multiple><br>

            <label for="youtubeLink">Youtube Link:</label>
            <input type="text" id="editYoutubeLink" required placeholder="Enter Youtube link...">

            <button id="saveButton">Save</button>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div id="privacyModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span id="closePrivacyModal" style="cursor: pointer;">&times;</span>
            <h2>Set Privacy for All Blogs</h2>
            <p>Choose the visibility for all your blogs:</p>
            <button id="makeAllPublic" class="privacy-action-button">Make All Public</button>
            <button id="makeAllPrivate" class="privacy-action-button">Make All Private</button>
        </div>
    </div>    

    <link rel="stylesheet" href="css/print-page.css">
    <script src="js/print-blogs.js"></script>
    <script>
    const baseUrl = '<?php echo $base_url; ?>';
    const blogMode = "<?= $_SESSION['BLOG_MODE']?>";
    console.log(blogMode);

    const fetchBlogs = (actionType, title = '', startDate = '', endDate = '', sortOrder = 'asc') => {
        fetch(
                `actions/${actionType}.php?title=${title}&start_date=${startDate}&end_date=${endDate}&sort_order=${sortOrder}`
            )
            .then(response => response.json())
            .then(blogPosts => {
                const postsContainer = document.getElementById('postsContainer');
                postsContainer.innerHTML = ''; // Clear previous posts

                if (blogPosts.message) {
                    const noResultsMessage = document.createElement('p');
                    noResultsMessage.textContent = blogPosts.message; // "No matching blogs found"
                    noResultsMessage.className = 'no-results-message';
                    postsContainer.appendChild(noResultsMessage);
                    return;
                }

                blogPosts.forEach(post => {
                    // set path to the newly uploaded image first
                    var path = baseUrl + `images/${post.blog_id}/${post.images[0]}`;
                    // if the newly uploaded image is gone, resort to default
                    if (post.images.length <= 0) {
                        path = baseUrl + "images/photoABCDLogo.png";
                    }
                    
                    const hasMultiplePhotos = post.images.length > 1;

                    let currentPhotoIndex = 0;

                    const blogContainer = document.createElement('div');
                    blogContainer.className = 'blog-container';

                    const blogUserContainer = document.createElement('div');
                    blogUserContainer.className = 'blog-user-container';

                    const email = post.creator_email;
                    function sanitizeEmailForFilename(email) {
                        return email.toLowerCase().replace(/[^a-z0-9]/g, '_');
                    }

                    // Construct the URL to get the latest image from the server-side script
                    const getImageUrl = `actions/get-latest-image.php?email=${encodeURIComponent(email)}`;

                    // Create user image element and default to blank icon initially
                    const userImage = document.createElement('img');
                    userImage.alt = 'User Image';
                    userImage.className = 'blog-user-image';

                    // Fetch the latest image from the server
                    fetch(getImageUrl)
                        .then(response => response.json())
                        .then(data => {
                            const userImagePath = data.image;

                            if (userImagePath) {
                                userImage.src = userImagePath;
                            } else {
                                userImage.src = 'images/blankicon.jpg'; // Fallback image
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching user image:', error);
                            userImage.src = 'images/blankicon.jpg'; // Fallback on error
                        });

                    const username = document.createElement('p');
                    username.className = 'blog-username';
                    username.textContent = post.creator_email;

                    const creationDate = document.createElement('p');
                    creationDate.className = 'blog-creation-date';
                    const creationDateObject = new Date(post.creation_date);
                    creationDate.textContent = creationDateObject.toLocaleDateString() + ' ' +
                        creationDateObject.toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                    const blogTitle = document.createElement('h2');
                    blogTitle.className = 'blog-title';
                    blogTitle.textContent = post.title;

                    const photoContainer = document.createElement('div');
                    photoContainer.className = 'photo-container';

                    const leftArrow = document.createElement('button');
                    leftArrow.className = 'photo-nav-button';
                    leftArrow.innerHTML = '&#9664;'; 
                    leftArrow.style.display = hasMultiplePhotos ? 'inline-block' : 'none';

                    const rightArrow = document.createElement('button');
                    rightArrow.className = 'photo-nav-button';
                    rightArrow.innerHTML = '&#9654;'; 
                    rightArrow.style.display = hasMultiplePhotos ? 'inline-block' : 'none';

                    const img = document.createElement('img');
                    img.src = path;
                    img.alt = 'Blog Image';
                    img.className = 'blog-photo';

                    const updatePhoto = () => {
                        img.src = baseUrl + `images/${post.blog_id}/${post.images[currentPhotoIndex]}`;
                    };

                    const blogDescription = document.createElement('p');
                    blogDescription.className = 'blog-description';
                    blogDescription.textContent = post.description;

                    // Create dropdown for edit/delete options
                    const optionsDropdown = document.createElement('div');
                    optionsDropdown.className = 'dropdown';

                    const optionsButton = document.createElement('button');
                    optionsButton.className = 'options-button';
                    optionsButton.innerHTML = '▼'; // Down caret

                    const dropdownContent = document.createElement('div');
                    dropdownContent.className = 'dropdown-content';

                    const printLink = document.createElement('a');
                    printLink.href = '#';
                    printLink.textContent = 'Print';
                    printLink.onclick = (e) => {
                        e.preventDefault();
                        printBlog(post); // Call the print function with the current post data
                    };

                    const editLink = document.createElement('a');
                    editLink.href = '#';
                    editLink.textContent = 'Edit';
                    editLink.onclick = (e) => {
                        e.preventDefault();
                        openEditModal(post); // Open the modal with the current post data
                    };

                    const deleteLink = document.createElement('a');
                    deleteLink.href = '#'; // Link to delete functionality
                    deleteLink.textContent = 'Delete';
                    deleteLink.onclick = (e) => {
                        e.preventDefault();
                        // Confirmation dialog
                        if (confirm('Are you sure you want to delete this blog post?')) {
                            deleteBlog(post.blog_id, post.creator_email, post.title, post
                                .description);
                        }
                    };
                    
                    dropdownContent.appendChild(printLink);
                    dropdownContent.appendChild(editLink);
                    dropdownContent.appendChild(deleteLink);
                    optionsDropdown.appendChild(optionsButton);
                    optionsDropdown.appendChild(dropdownContent);

                    // Append elements to blogContainer
                    blogUserContainer.appendChild(userImage);
                    blogUserContainer.appendChild(username);
                    blogUserContainer.appendChild(creationDate);
                    blogContainer.appendChild(blogUserContainer);
                    blogContainer.appendChild(blogTitle);
                    blogContainer.appendChild(photoContainer);
                    blogContainer.appendChild(blogDescription);
                    // blogContainer.appendChild(eventDate); // Removed event date
                    blogContainer.appendChild(optionsDropdown); // Add dropdown to blogContainer

                    
                    /**
                     * Checks if blog video URL is a valid youtube URL.
                     * 
                     * If the URL is null, the entry is ignored and not displayed.
                     * 
                     * If the URL is a valid youtube WATCH link, then its converted into
                     * a proper youtube EMBED link.
                     * 
                     * If the URL is proper, it's returned as is.
                     * 
                     * If the URL doesn't meet any of these conditions and is not null,
                     * a default URL is used instead.
                     */
                    function validateVidUrl() {
                        var vidUrl = post.youtube_link;
                        if (vidUrl === null) {
                            return null;

                        } else if (vidUrl.includes("youtube.com/watch")) {
                            var newUrl = vidUrl.split("&ab_channel")[0].split("watch?v=");
                            vidUrl = newUrl[0] + "embed/" + newUrl[1];
                            console.log(vidUrl);
                            return vidUrl;

                        } else if (vidUrl.includes("youtube.com/embed")) {
                            return vidUrl;

                        } else {
                            return null;
                        }
                    }

                    switch (blogMode)
                    {
                        case "Videos":
                            if (post.youtube_link !== null)
                            {
                                const blogVideo = document.createElement('iframe');
                                var vidUrl = validateVidUrl();
                                blogVideo.src = vidUrl;
                                
                                if (vidUrl !== null) {
                                    photoContainer.appendChild(blogVideo);
                                } else {
                                    img.src = baseUrl + `images/photoABCDLogo.png`;
                                    photoContainer.appendChild(img);
                                }

                                postsContainer.appendChild(blogContainer);
                                
                            }
                            break;
                        
                        case "Mixed":
                            leftArrow.onclick = () => {
                                currentPhotoIndex = (currentPhotoIndex - 1 + post.images.length) % post.images.length;
                                updatePhoto();
                            };

                            rightArrow.onclick = () => {
                                currentPhotoIndex = (currentPhotoIndex + 1) % post.images.length;
                                updatePhoto();
                            };

                            photoContainer.appendChild(leftArrow);
                            photoContainer.appendChild(img);
                            photoContainer.appendChild(rightArrow);

                            const videoContainer = document.createElement('div');
                            videoContainer.classList.add('video-container');

                            // If there is a video, add it in
                            const videoUrl = validateVidUrl();
                            if (videoUrl !== null) {
                                const blogVideo = document.createElement('iframe');
                                blogVideo.src = videoUrl;
                                blogVideo.classList.add('video');
                                videoContainer.appendChild(blogVideo);
                            }

                            blogContainer.appendChild(videoContainer);
                            postsContainer.appendChild(blogContainer);
                            break;
                        
                        case "Photos":
                            leftArrow.onclick = () => {
                                currentPhotoIndex = (currentPhotoIndex - 1 + post.images.length) % post.images.length;
                                updatePhoto();
                            };

                            rightArrow.onclick = () => {
                                currentPhotoIndex = (currentPhotoIndex + 1) % post.images.length;
                                updatePhoto();
                            };

                            photoContainer.appendChild(leftArrow);
                            photoContainer.appendChild(img);
                            photoContainer.appendChild(rightArrow);
                            postsContainer.appendChild(blogContainer);
                            break;
                    }


                });
            })
            .catch(error => console.error('Error fetching blog posts:', error));
    };


    fetchBlogs('get-my-blogs');

    document.addEventListener('DOMContentLoaded', () => {
        const privacyButton = document.getElementById('privacyButton');
        const privacyModal = document.getElementById('privacyModal');
        const closeModal = document.getElementById('closePrivacyModal');
        const makeAllPublic = document.getElementById('makeAllPublic');
        const makeAllPrivate = document.getElementById('makeAllPrivate');

        // Open Privacy Modal
        privacyButton.addEventListener('click', () => {
            privacyModal.style.display = 'flex';
        });

        // Close Privacy Modal
        closeModal.addEventListener('click', () => {
            privacyModal.style.display = 'none';
        });

        // Handle "Make All Public" action
        makeAllPublic.addEventListener('click', () => {
            // Ask for confirmation before proceeding
            const confirmAction = confirm('Are you sure you want to make ALL your blogs public? This action cannot be undone.');
            if (confirmAction) {
                updatePrivacyForAll('public');
            }
        });

        // Handle "Make All Private" action
        makeAllPrivate.addEventListener('click', () => {
            // Ask for confirmation before proceeding
            const confirmAction = confirm('Are you sure you want to make ALL your blogs private? This action cannot be undone.');
            if (confirmAction) {
                updatePrivacyForAll('private');
            }
        });

        // Function to update privacy for all blogs
        function updatePrivacyForAll(privacyStatus) {
            const url = baseUrl + 'actions/update-privacy.php';
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ privacy: privacyStatus }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`All blogs have been made ${privacyStatus}.`);
                        privacyModal.style.display = 'none';
                        fetchBlogs('get-my-blogs');
                    } else {
                        alert('Failed to update privacy settings.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });


    document.getElementById('printBlogsButton').addEventListener('click', async function() {
        const blogData = await getBlogData('date_asc'); //ensure blogs are sorted by event_date asc.
        if (blogData.length > 0) {
            printBlogs(blogData); // Ensure this is defined
        } else {
            alert('No blogs available to print');
        }
    });

    async function getBlogData(sortOrder) {
        try {
            const url = baseUrl + 'actions/get-my-blogs.php?sort_order' + sortOrder; 
            
            // Fetch blog data from the server
            const response = await fetch(url);

            // Check if the response was successful
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`);
            }

            // Parse the JSON response
            const blogData = await response.json();

            // Return the blog data
            return blogData;

        } catch (error) {
            // Log the error for debugging
            console.error('Error fetching blog data:', error);
            alert('Error fetching blog data: ' + error.message);
            return [];  // Return an empty array if there was an error
        }
    }




    const printBlog = (post) => {
        // Set base URL for images
        var path = baseUrl + `images/${post.blog_id}/${post.images[0]}`;
        // if the newly uploaded image is gone, resort to default
        if (post.images.length <= 0) {
            path = baseUrl + "images/photoABCDLogo.png";
        }

            // Generate the HTML content for multiple images
        let imagesHtml = '';
        if (post.images && post.images.length > 0) {
            imagesHtml = post.images.map(image => {
                if (image && typeof image === 'string' && image.trim() !== '') {
                    const imageSrc = `${baseUrl}images/${post.blog_id}/${image}`;
                    return `
                        <img src="${imageSrc}" alt="Blog Image" style="width:100%;height:auto;margin-bottom:20px;" 
                            onerror="this.onerror=null;this.src='${path}';">
                    `;
                }
                return ''; // Skip invalid images
            }).join('');
        }else {
        // Fallback to the default image if no valid images are found
        imagesHtml = `
            <img src="${path}" alt="Default Blog Image" style="max-width:100%;height:auto;">
        `;
        }


        // Generate the complete HTML for printing
        const printContent = `
            <div>
                <h2>${post.title}</h2>
                <p><strong>Created by:</strong> ${post.creator_email}</p>
                <p><strong>Event Date:</strong> ${post.event_date}</p>
                <p><strong>Description:</strong> ${post.description}</p>
                ${imagesHtml}
            </div>
        `;

        // Open a new window and apply print-specific styles
        const newWindow = window.open('', '_blank');
        newWindow.document.write(`
            <html>
                <head>
                    <title>Print Blog</title>
                    <style>
                        @media print {
                            body {
                                margin: 0;
                                padding: 0;
                            }
                            img {
                                page-break-inside: avoid; /* Prevent images from splitting across pages */
                                max-width: 100%;
                                height: auto;
                            }
                            div {
                                page-break-after: auto; /* Allow page breaks between blog posts */
                            }
                        }
                    </style>
                </head>
                <body>${printContent}</body>
            </html>
        `);
        newWindow.document.close();
        newWindow.print();
    };

    document.getElementById('searchButton').addEventListener('click', () => {
        const title = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const sortOrder = document.getElementById('sortOrder').value;
        fetchBlogs('get-my-blogs', title, startDate, endDate, sortOrder);
    });

    document.getElementById('searchInput').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const title = document.getElementById('searchInput').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const sortOrder = document.getElementById('sortOrder').value;
            fetchBlogs('get-my-blogs', title, startDate, endDate, sortOrder);
        }
    });

    document.getElementById('sortOrder').addEventListener('change', () => {
        const title = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const sortOrder = document.getElementById('sortOrder').value;
        fetchBlogs('get-my-blogs', title, startDate, endDate, sortOrder);
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Toggle the dropdown menu visibility when the button is clicked
        document.addEventListener('click', function(event) {
            // Get all dropdowns
            const dropdowns = document.querySelectorAll('.dropdown-content');

            // Close all dropdowns if the click is outside
            dropdowns.forEach(dropdown => {
                if (!dropdown.parentElement.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // If the clicked element is the dropdown button, toggle its dropdown
            const optionsButton = event.target.closest('.options-button');
            if (optionsButton) {
                const dropdownContent = optionsButton.nextElementSibling;
                dropdownContent.style.display = dropdownContent.style.display === 'block' ?
                    'none' : 'block';
            }
        });
    });

    const updatePhotoDisplay = (photos, currentPhotoIndex, blogId) => {
        const photoDisplay = document.getElementById('photoDisplay');
        const deletePhotoButton = document.getElementById('deletePhotoButton');
        const defaultImagePath = baseUrl + 'images/photoABCDLogo.png';

        if (photos.length > 0) {
            photoDisplay.src = baseUrl + `images/${blogId}/${photos[currentPhotoIndex]}`;
            // Check if the displayed photo is the default image
            if (photoDisplay.src === defaultImagePath) {
                deletePhotoButton.style.display = 'none';
            } else {
                deletePhotoButton.style.display = 'inline-block';
            }
        } else {
            photoDisplay.src = defaultImagePath; // Default photo if no images
            deletePhotoButton.style.display = 'none';
        }

        // Show or hide navigation buttons based on the current photo index
        document.getElementById('prevPhoto').style.display = currentPhotoIndex > 0 ? 'inline-block' : 'none';
        document.getElementById('nextPhoto').style.display = currentPhotoIndex < photos.length - 1 ? 'inline-block' : 'none';
    };



    const openEditModal = (post) => {
        document.getElementById('editModal').style.display = 'block';

        // Populate the fields with the existing data from the post object
        document.getElementById('blogId').value = post.blog_id; // Should be defined
        document.getElementById("editTitle").value = post.title; // Auto-fill title with current title
        document.getElementById("editDescription").value = post.description; // Auto-fill description with current description
        document.getElementById('privacyFilter').value = post.privacy_filter; // Set privacy filter value

        document.getElementById('creatorEmail').value = post.creator_email; // Populate creator email
        document.getElementById('eventDate').value = post.event_date; // Populate event date
        document.getElementById('creationDate').value = post.creation_date; // Populate creation date

        document.getElementById('editYoutubeLink').value = post.youtube_link;

        // Set up photo gallery
        const photos = post.images || []; // If no photos, use empty array
        let currentPhotoIndex = 0; // Default to the first photo

        // Call updatePhotoDisplay to initialize the photo display
        updatePhotoDisplay(photos, currentPhotoIndex, post.blog_id);

        // Next and previous photo navigation
        document.getElementById('prevPhoto').onclick = () => {
            if (currentPhotoIndex > 0) {
                currentPhotoIndex--;
                updatePhotoDisplay(photos, currentPhotoIndex, post.blog_id);
            }
        };

        document.getElementById('nextPhoto').onclick = () => {
            if (currentPhotoIndex < photos.length - 1) {
                currentPhotoIndex++;
                updatePhotoDisplay(photos, currentPhotoIndex, post.blog_id);
            }
        };
    };


    document.getElementById('deletePhotoButton').onclick = () => {
        if (confirm('Are you sure you want to delete this photo?')) {
            const blogId = document.getElementById('blogId').value;
            const photoPath = document.getElementById('photoDisplay').src.replace(window.location.origin, '');

            fetch(`actions/delete-photo.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    blogId,
                    photoPath,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Photo deleted successfully!');
                        // Fetch updated blog data and update the modal with the new photos
                        const photos = data.remainingPhotos || [];
                        let currentPhotoIndex = 0; // Reset to first photo
                        updatePhotoDisplay(photos, currentPhotoIndex, blogId);

                        // If there are no photos left, hide the delete button and navigation
                        if (photos.length === 0) {
                            document.getElementById('deletePhotoButton').style.display = 'none';
                            document.getElementById('prevPhoto').style.display = 'none';
                            document.getElementById('nextPhoto').style.display = 'none';
                        }
                    } else {
                        alert('Failed to delete photo. Please try again.');
                    }
                })
                .catch(error => console.error('Error deleting photo:', error));
        }
    };

    const closeModal = () => {
        // Hide the modal
        document.getElementById('editModal').style.display = 'none';
        // Reset all modal fields
        clearEditModal();
    };


    const clearEditModal = () => {
        // Clear text fields
        document.getElementById("editTitle").value = ''; 
        document.getElementById("editDescription").value = ''; 
        document.getElementById('eventDate').value = ''; 
        document.getElementById('privacyFilter').value = 'public'; 
        document.getElementById('editYoutubeLink').value = ''; 

        // Hidden fields
        document.getElementById('creatorEmail').value = ''; 
        document.getElementById('creationDate').value = ''; 

        // Reset photo display
        const photoDisplay = document.getElementById('photoDisplay');
        const defaultImagePath = baseUrl + 'images/photoABCDLogo.png';
        photoDisplay.src = defaultImagePath; 

        // Hide navigation and delete buttons
        document.getElementById('deletePhotoButton').style.display = 'none';
        document.getElementById('prevPhoto').style.display = 'none';
        document.getElementById('nextPhoto').style.display = 'none';

        // Clear file input
        const uploadPhotoInput = document.getElementById('photoUpload');
        uploadPhotoInput.value = ''; 

        
        window.currentPhotoIndex = 0; 
    };

    window.onclick = (event) => {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeModal(); // Close modal if user clicks outside of it
            fetchBlogs('get-my-blogs');
        }
    };


    document.getElementById('closeModal').onclick = () => {
        closeModal();
    };


    document.getElementById('saveButton').onclick = () => {
        event.preventDefault();

        const blogId = document.getElementById('blogId').value;
        var title = document.getElementById('editTitle').value;
        var description = document.getElementById('editDescription').value;
        const privacyFilter = document.getElementById('privacyFilter').value;
        const creatorEmail = document.getElementById('creatorEmail').value;
        const eventDate = document.getElementById('eventDate').value;
        const creationDate = document.getElementById('creationDate').value;
        const youtubeLink = document.getElementById('editYoutubeLink').value;

        //validation of title/descrption/date
        if (isFieldEmpty()) {
            alert('Title and Description cannot be empty!');
        } else if (!checkTitle()) {
            alert('Title must start with a letter or number only.');
        } else if (eventDateNull()) {
            alert('Event must have a date');
        } else {
            fetch(`actions/update-blog.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        blogId,
                        title,
                        description,
                        privacyFilter,
                        creatorEmail,
                        eventDate,
                        creationDate,
                        youtubeLink
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Blog post info updated successfully!');
                        fetchBlogs('get-my-blogs'); // Reload blogs

                        // Photo upload handling
                        const photoUpload = document.getElementById('photoUpload').files;
                        if (photoUpload.length > 0) {
                            const formData = new FormData();
                            for (let i = 0; i < photoUpload.length; i++) {
                                formData.append('photos[]', photoUpload[i]); // Using 'photos[]' to handle multiple files
                            }
                            formData.append('blog_id', blogId);

                            // Upload the photos
                            fetch('actions/upload-photo.php', {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(photoData => {
                                    if (photoData.success) {
                                        alert('Blog photos updated successfully!');
                                        closeModal();
                                        fetchBlogs('get-my-blogs');
                                    } else {
                                        alert('Failed to upload photos: ' + photoData.message);
                                        closeModal();
                                    }
                                })
                                .catch(error => console.error('Error uploading photos:', error));
                        }


                        document.getElementById('editModal').style.display = 'none'; // Close modal
                    } else {
                        alert('Failed to update blog post: ' + data.message);
                    }
                })
                .catch(error => console.error('Error updating blog post:', error));

        }


        function eventDateNull() {
            if (eventDate === '') {
                return true;
            } else {
                return false;
            }
        }


        function isFieldEmpty() {
            if (title === '' || description === '') {
                return true;
            } else {
                return false;
            }

        }

        function checkTitle() {

            if (!/^[a-zA-Z0-9].*/.test(title.trim()[0])) {

                return false;

            } else {
                return true;
            }

        }


    };


    const deleteBlog = (blogId, creatorEmail, title, description) => {
        fetch(`actions/delete-blog.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    blogId,
                    creatorEmail,
                    title,
                    description,
                    deleteBlog: 'yes', // Flag to indicate deletion
                }),
            })
            .then(response => response.json())
            .then(data => {
                //("Response from server:", data); // Debug log
                if (data.success) {
                    alert('Blog post deleted successfully!');
                    // Reload the posts to reflect changes
                    fetchBlogs('get-my-blogs');
                } else {
                    alert('Failed to delete blog post: ' + data.message);
                }
            })
            .catch(error => console.error('Error deleting blog post:', error));
    };
    </script>
</div>