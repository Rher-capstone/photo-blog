<?php
/*
$host = $_SERVER['HTTP_HOST'];
$is_localhost = ($host == 'localhost' || $host == '127.0.0.1');

// If the server is localhost, include 'photo_abcd_A' in the base URL
$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $host . ($is_localhost ? '/photo_abcd_A' : '');
$base_url = rtrim($base_url, '/') . '/'; // Ensure single trailing slash
*/
$blankIcon = $base_url . 'images/blankicon.jpg';
if (!isset($_GET['blog_pairs'])) {include_once('actions/get-blogs-modular.php');}
include ('view-profile-modal.php');
?>

<div class="container" id="main-content">
    <?php include_once('sort-util-bar.php')?>
    
    <!-- Posts Container -->
    <div id="postsContainer"></div>
    <div class="row" id="blog-row"></div>
    <!-- Modal for Photo Only View -->
    <div class="modal fade" id="card-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id='card-modal-title'></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" alt="" id='card-modal-img' class="img-fluid">
                    <p class="lead" id='card-modal-desc'></p>
                </div>
                <div class="modal-footer">
                    <p id='card-modal-email'></p>
                    <ul class="pagination" id='card-modal-img-nav'>
                        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Section -->
    <script type="text/javascript">
    // Assign PHP variables to JavaScript variables
    const blogModular = <?php echo $_GET['blog_pairs']; ?>;
    const blogRow = document.getElementById('blog-row');

    var baseUrl = '<?php echo $base_url; ?>';
    var blankIcon = '<?php echo $blankIcon; ?>';
    let actionType1 = 'get-blogs'; // Declare actionType once
    let actionType2 = 'get-blogs-modular';
    let creatorId = '';

    // Ensure baseUrl ends with a single slash
    if (!baseUrl.endsWith('/')) {
        baseUrl += '/';
    }

    // Event listener for view options change
    document.getElementById('viewOptions').addEventListener('change', function() {
        const viewOption = this.value;
        if (viewOption === 'photoOnly') {
            loadPhotoOnlyView();
        } else {
            loadTraditionalView();
        }
    });

    // Function to load traditional view
    function loadTraditionalView() {
        const title = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const sortOrder = document.getElementById('sortOrder').value;
        const viewOptions = 'traditional';
        fetchBlogs(title, startDate, endDate, sortOrder, viewOptions);
    }

    // Function to load photo-only view
    function loadPhotoOnlyView() {
        const title = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const sortOrder = document.getElementById('sortOrder').value;
        const viewOptions = 'photoOnly';
        fetchBlogs(title, startDate, endDate, sortOrder, viewOptions);
    }

    // Function to load profile view
    function loadProfileView(username) {
        const profileTitle = document.getElementById('profileSearchInput').value;
        const profileStartDate = document.getElementById('profileStartDate').value;
        const profileEndDate = document.getElementById('profileEndDate').value;
        const profileSortOrder = document.getElementById('profileSortOrder').value;
        creatorId = username;
        fetchProfileBlogs(profileTitle, profileStartDate, profileEndDate, profileSortOrder, creatorId);
    }

    /**
     * constant: 1-3 options: Video, Mixed, Photo
     * 
     * Switch Statement: constant
     *      constant viewOptions
     */

    // Function to fetch blog posts with sorting and view options
    const fetchBlogs = (title = '', startDate = '', endDate = '', sortOrder = 'asc', viewOptions = 'traditional') => {
        fetch(
                `actions/${actionType1}.php?title=${encodeURIComponent(title)}&start_date=${startDate}&end_date=${endDate}&sort_order=${sortOrder}&view_options=${viewOptions}`
            )
            .then(response => response.json())
            .then(blogPosts => {
                const postsContainer = document.getElementById('postsContainer');
                postsContainer.innerHTML = ''; // Clear previous posts

                if (blogPosts.message) {
                    // Display a no-results message
                    const noResultsMessage = document.createElement('p');
                    noResultsMessage.textContent = blogPosts.message;
                    noResultsMessage.className = 'no-results-message';
                    postsContainer.appendChild(noResultsMessage);
                    return;
                }
                if (viewOptions === 'photoOnly') {
                    displaySortedBlogs(sortOrder, blogModular, blogPosts);

                } else if (viewOptions === "traditional" && (blogMode !== "Videos" && blogMode !== "Mixed")) {
                    displayTraditionalView(blogModular, postsContainer, sortOrder, blogPosts);

                } else if (viewOptions === "traditional" && blogMode === "Videos") {
                    displayVideoView(blogModular, postsContainer, sortOrder, blogPosts, blogMode);

                } else if (viewOptions === "traditional" && blogMode === "Mixed") {
                    displayMixedView(blogModular, postsContainer, sortOrder, blogPosts, blogMode);
                }

            })
            .catch(error => console.error('Error fetching blog posts:', error));
    };

    // Function to fetch blog posts with sorting and view options
    const fetchProfileBlogs = (profileTitle = '', profileStartDate = '', profileEndDate = '', profileSortOrder =
        'asc', creatorId = '') => {
        fetch(
                `actions/get-profile-blogs.php?title=${encodeURIComponent(profileTitle)}&start_date=${profileStartDate}&end_date=${profileEndDate}&sort_order=${profileSortOrder}&creator_id=${encodeURIComponent(creatorId)}`
            )
            .then(response => response.json())
            .then(profilePosts => {
                const profileContainer = document.getElementById('profilePostsContainer');
                profileContainer.innerHTML = ''; // Clear previous posts

                if (profilePosts.message) {
                    // Display a no-results message
                    const noResultsMessage = document.createElement('p');
                    noResultsMessage.textContent = profilePosts.message;
                    noResultsMessage.className = 'no-results-message';
                    profileContainer.appendChild(noResultsMessage);
                    return;
                }

                // Call function to display posts as grid (without clickable actions)
                displayProfileBlogs(profileSortOrder, blogModular, profilePosts);

            })
            .catch(error => console.error('Error fetching blog posts:', error));
    };

    // Function to display traditional view
    function displayTraditionalView(blogModular, postsContainer, sortOrder, blogPosts) {
        blogRow.innerHTML = ''; // Clear the container
        let combinedGet = [];

        // Sort blogs based on title in ascending or descending order
        for (let j = 0; j < blogPosts.length; j++) {
            for (let i = 0; i < blogModular.length; i++) {
                if (blogPosts[j].blog_id === blogModular[i].table.blog_id) {
                    combinedGet.push(blogModular[i])
                    break;
                }
            }
        }

        combinedGet.sort((a, b) => {
            const titleA = a.table.title.toLowerCase();
            const titleB = b.table.title.toLowerCase();
            const dateA = a.table.event_date;
            const dateB = b.table.event_date;
            if (sortOrder === 'asc') {
                return titleA < titleB ? -1 : (titleA > titleB ? 1 : 0);
            } else if (sortOrder === 'desc') {
                return titleA > titleB ? -1 : (titleA < titleB ? 1 : 0);
            } else if (sortOrder === 'date_asc') {
                return dateA < dateB ? -1 : (dateA > dateB ? 1 : 0);
            } else {
                return dateA > dateB ? -1 : (dateA < dateB ? 1 : 0);
            }
        });

        combinedGet.forEach(post => {
            const table = post.table;
            const blogContainer = document.createElement('div');
            blogContainer.className = 'blog-container';

            const blogUserContainer = document.createElement('div');
            blogUserContainer.className = 'blog-user-container';

            const email = table.creator_email;
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
                    console.log('Fetched image URL:', data.image);
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
            username.textContent = table.creator_email;

            // Add hover effect to create green glow around the username
            username.addEventListener('mouseover', () => {
                username.style.boxShadow = '0 0 8px 8px rgba(228, 253, 236, 1)';
            });
            username.addEventListener('mouseout', () => {
                username.style.boxShadow = '';
            });

            // Click listener for each username
            username.addEventListener('click', () => {
                // Open the modal
                $('#viewProfileModal').modal('show');
                loadProfileView(table.creator_email);
            });

            function formatCreationDate(dateString) {
                const date = new Date(dateString);
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const year = date.getFullYear();
                return `${month}/${day}/${year}`;
            }

            function formatCreationTime(dateString) {
                const date = new Date(dateString);
                let hours = date.getHours();
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert to 12-hour format
                hours = hours % 12;
                hours = hours ? String(hours).padStart(2, '0') : '12';

                return `${hours}:${minutes} ${ampm}`;
            }

            const creationDate = document.createElement('p');
            creationDate.className = 'blog-creation-date';
            creationDate.textContent = '   ' + formatCreationDate(table.creation_date) + ' ◦ ' +
                formatCreationTime(table.creation_date);

            const blogTitle = document.createElement('h2');
            blogTitle.className = 'blog-title';
            blogTitle.textContent = table.title;

            const imageContainer = document.createElement('div');
            imageContainer.className = 'image-container';

            const leftArrow = document.createElement('span');
            leftArrow.className = 'left-arrow';
            leftArrow.innerHTML = '&#9664;';
            leftArrow.style.display = 'none';

            const rightArrow = document.createElement('span');
            rightArrow.className = 'right-arrow';
            rightArrow.innerHTML = '&#9654;';
            rightArrow.style.display = 'none';

            const img = document.createElement('img');
            const images = post.images;
            var img_src;

            // define path for default image if none is uploaded
            if (images.img_names.length === 0) {
                img_src = 'images/photoABCDLogo.png';
            } else {
                img_src = `${images.dir}${images.img_names[0]}`;
            }
            img.src = img_src;

            img.alt = 'Blog Image';
            img.className = 'blog-photo';
            
            fetch(`actions/count-files.php?blog_id=${table.blog_id}`)
                .then(response => response.json())
                .then(data => {
                    const fileCount = data.fileCount;
                    if (fileCount > 1) {
                        leftArrow.style.display = 'inline';
                        rightArrow.style.display = 'inline';
                    }

                    let currentImageIndex = 0;

                    // Left arrow click event
                    leftArrow.addEventListener('click', () => {
                        currentImageIndex--;
                        if (currentImageIndex < 0) {
                            currentImageIndex = fileCount - 1;
                            img.src = `${images.dir}${images.img_names[currentImageIndex]}`;
                        } else if (currentImageIndex == 0) {
                            img.src = `${images.dir}${images.img_names[0]}`;
                        } else {
                            img.src = `${images.dir}${images.img_names[currentImageIndex]}`;
                        }
                    });

                    // Right arrow click event
                    rightArrow.addEventListener('click', () => {
                        currentImageIndex++;
                        if (currentImageIndex == fileCount) {
                            currentImageIndex = 0; // Reset to zero if it reaches fileCount
                            img.src = `${images.dir}${images.img_names[0]}`;
                        } else {
                            img.src = `${images.dir}${images.img_names[currentImageIndex]}`;
                        }
                    });
                })
                .catch(error => console.error('Error fetching file count:', error));

            const blogDescription = document.createElement('p');
            blogDescription.className = 'blog-description';
            blogDescription.textContent = table.description;

            const blogSeparator = document.createElement('hr');
            blogSeparator.className = 'blog-separator';

            blogUserContainer.appendChild(userImage);
            blogUserContainer.appendChild(username);
            blogUserContainer.appendChild(creationDate);
            imageContainer.appendChild(leftArrow);
            imageContainer.appendChild(img);
            imageContainer.appendChild(rightArrow);
            blogContainer.appendChild(blogUserContainer);
            blogContainer.appendChild(blogTitle);
            blogContainer.appendChild(imageContainer);
            blogContainer.appendChild(blogDescription);

            postsContainer.appendChild(blogContainer);
            postsContainer.appendChild(blogSeparator);
        });
    }

    // Display video mode
    function displayVideoView(blogModular, postsContainer, sortOrder, blogPosts) {
        blogRow.innerHTML = ''; // Clear the container
        let combinedGet = [];

        // Sort blogs based on title in ascending or descending order
        for (let j = 0; j < blogPosts.length; j++) {
            for (let i = 0; i < blogModular.length; i++) {
                if (blogPosts[j].blog_id === blogModular[i].table.blog_id) {
                    combinedGet.push(blogModular[i])
                    break;
                }
            }
        }

        combinedGet.sort((a, b) => {
            const titleA = a.table.title.toLowerCase();
            const titleB = b.table.title.toLowerCase();
            const dateA = a.table.event_date;
            const dateB = b.table.event_date;
            if (sortOrder === 'asc') {
                return titleA < titleB ? -1 : (titleA > titleB ? 1 : 0);
            } else if (sortOrder === 'desc') {
                return titleA > titleB ? -1 : (titleA < titleB ? 1 : 0);
            } else if (sortOrder === 'date_asc') {
                return dateA < dateB ? -1 : (dateA > dateB ? 1 : 0);
            } else {
                return dateA > dateB ? -1 : (dateA < dateB ? 1 : 0);
            }
        });

        combinedGet.forEach(post => {
            const table = post.table;
            const blogContainer = document.createElement('div');
            blogContainer.className = 'blog-container';

            const blogUserContainer = document.createElement('div');
            blogUserContainer.className = 'blog-user-container';

            const email = table.creator_email;
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
                    console.log('Fetched image URL:', data.image);
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
            username.textContent = table.creator_email;

            // Add hover effect to create green glow around the username
            username.addEventListener('mouseover', () => {
                username.style.boxShadow = '0 0 8px 8px rgba(228, 253, 236, 1)';
            });
            username.addEventListener('mouseout', () => {
                username.style.boxShadow = '';
            });

            // Click listener for each username
            username.addEventListener('click', () => {
                // Open the modal
                $('#viewProfileModal').modal('show');
                loadProfileView(table.creator_email);
            });

            function formatCreationDate(dateString) {
                const date = new Date(dateString);
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const year = date.getFullYear();
                return `${month}/${day}/${year}`;
            }

            function formatCreationTime(dateString) {
                const date = new Date(dateString);
                let hours = date.getHours();
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert to 12-hour format
                hours = hours % 12;
                hours = hours ? String(hours).padStart(2, '0') : '12';

                return `${hours}:${minutes} ${ampm}`;
            }

            function validateVidUrl() {
                var vidUrl = table.youtube_link;
                if (vidUrl === null) {
                    return null;
                } else if (vidUrl.includes("youtube.com/watch")) {
                    var newUrl = vidUrl.split("&ab_channel")[0].split("watch?v=");
                    vidUrl = newUrl[0] + "embed/" + newUrl[1];
                    console.log(newUrl[1]);
                    return vidUrl;
                } else if (vidUrl.includes("youtube.com/embed")) {
                    return vidUrl;
                } else {
                    return null;
                }
            }

            const creationDate = document.createElement('p');
            creationDate.className = 'blog-creation-date';
            creationDate.textContent = '   ' + formatCreationDate(table.creation_date) + ' ◦ ' +
                formatCreationTime(table.creation_date);

            const blogTitle = document.createElement('h2');
            blogTitle.className = 'blog-title';
            blogTitle.textContent = table.title;

            const imageContainer = document.createElement('div');
            imageContainer.className = 'image-container';

            const videoUrl = validateVidUrl(); // Get the validated video URL
            if (videoUrl) {
                // If there is a valid video URL, create and append the iframe
                const blogVideo = document.createElement('iframe');
                blogVideo.src = videoUrl;
                imageContainer.appendChild(blogVideo);
            } 
            
            // else {
            //     // If no valid video URL, display the default image
            //     const img = document.createElement('img');
            //     img.src = 'images/photoABCDLogo.png';
            //     img.alt = 'Blog Image';
            //     img.className = 'blog-photo';
            //     imageContainer.appendChild(img);
            // }

            const blogDescription = document.createElement('p');
            blogDescription.className = 'blog-description';
            blogDescription.textContent = table.description;

            const blogSeparator = document.createElement('hr');
            blogSeparator.className = 'blog-separator';

            blogUserContainer.appendChild(userImage);
            blogUserContainer.appendChild(username);
            blogUserContainer.appendChild(creationDate);
            blogContainer.appendChild(blogUserContainer);
            blogContainer.appendChild(blogTitle);
            blogContainer.appendChild(imageContainer);
            blogContainer.appendChild(blogDescription);

            if (videoUrl) {
                postsContainer.appendChild(blogContainer);
                postsContainer.appendChild(blogSeparator);
            }
        
        });

    }


    // Display Mixed mode
    function displayMixedView(blogModular, postsContainer, sortOrder, blogPosts) {
        blogRow.innerHTML = ''; // Clear the container
        let combinedGet = [];

        // Sort blogs based on title in ascending or descending order
        for (let j = 0; j < blogPosts.length; j++) {
            for (let i = 0; i < blogModular.length; i++) {
                if (blogPosts[j].blog_id === blogModular[i].table.blog_id) {
                    combinedGet.push(blogModular[i])
                    break;
                }
            }
        }

        combinedGet.sort((a, b) => {
            const titleA = a.table.title.toLowerCase();
            const titleB = b.table.title.toLowerCase();
            const dateA = a.table.event_date;
            const dateB = b.table.event_date;
            if (sortOrder === 'asc') {
                return titleA < titleB ? -1 : (titleA > titleB ? 1 : 0);
            } else if (sortOrder === 'desc') {
                return titleA > titleB ? -1 : (titleA < titleB ? 1 : 0);
            } else if (sortOrder === 'date_asc') {
                return dateA < dateB ? -1 : (dateA > dateB ? 1 : 0);
            } else {
                return dateA > dateB ? -1 : (dateA < dateB ? 1 : 0);
            }
        });

        combinedGet.forEach(post => {
            const table = post.table;
            const blogContainer = document.createElement('div');
            blogContainer.className = 'blog-container';

            const blogUserContainer = document.createElement('div');
            blogUserContainer.className = 'blog-user-container';

            const email = table.creator_email;
            function sanitizeEmailForFilename(email) {
                return email.toLowerCase().replace(/[^a-z0-9]/g, '_');
            }

            // Construct the URL to get the latest image from the server-side script
            const getImageUrl = `actions/get-latest-image.php?email=${encodeURIComponent(email)}`;

            // Create user image element and default to blank icon initially
            const userImage = document.createElement('img');
            userImage.alt = 'User Image';
            userImage.className = 'blog-user-image';

            const username = document.createElement('p');
            username.className = 'blog-username';
            username.textContent = table.creator_email;

            // Add hover effect to create green glow around the username
            username.addEventListener('mouseover', () => {
                username.style.boxShadow = '0 0 8px 8px rgba(228, 253, 236, 1)';
            });
            username.addEventListener('mouseout', () => {
                username.style.boxShadow = '';
            });

            // Click listener for each username
            username.addEventListener('click', () => {
                // Open the modal
                $('#viewProfileModal').modal('show');
                loadProfileView(table.creator_email);
            });

            fetch(getImageUrl)
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched image URL:', data.image);
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

            function formatCreationDate(dateString) {
                const date = new Date(dateString);
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const year = date.getFullYear();
                return `${month}/${day}/${year}`;
            }

            function formatCreationTime(dateString) {
                const date = new Date(dateString);
                let hours = date.getHours();
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';

                // Convert to 12-hour format
                hours = hours % 12;
                hours = hours ? String(hours).padStart(2, '0') : '12';

                return `${hours}:${minutes} ${ampm}`;
            }

            const creationDate = document.createElement('p');
            creationDate.className = 'blog-creation-date';
            creationDate.textContent = '   ' + formatCreationDate(table.creation_date) + ' ◦ ' +
                formatCreationTime(table.creation_date);

            const blogTitle = document.createElement('h2');
            blogTitle.className = 'blog-title';
            blogTitle.textContent = table.title;

           const imageContainer = document.createElement('div');
            imageContainer.className = 'image-container';

            function validateVidUrl() {
                var vidUrl = table.youtube_link;
                if (vidUrl === null) {
                    return null;
                }

                if (vidUrl.includes("youtube.com/watch")) {
                    var newUrl = vidUrl.split("&ab_channel")[0].split("watch?v=");
                    return newUrl[0] + "embed/" + newUrl[1]; // Return embed URL
                }

                if (vidUrl.includes("youtube.com/embed")) {
                    return vidUrl; // Return the existing embed URL
                }

                return null; // Return null for non-YouTube links
            }

            const leftArrow = document.createElement('span');
            leftArrow.className = 'left-arrow';
            leftArrow.innerHTML = '&#9664;';
            leftArrow.style.display = 'none';

            const rightArrow = document.createElement('span');
            rightArrow.className = 'right-arrow';
            rightArrow.innerHTML = '&#9654;';
            rightArrow.style.display = 'none';

            const img = document.createElement('img');
            const images = post.images;
            var img_src;

            // define path for default image if none is uploaded
            if (images.img_names.length === 0) {
                img_src = 'images/photoABCDLogo.png';
            } else {
                img_src = `${images.dir}${images.img_names[0]}`;
            }
            img.src = img_src;

            img.alt = 'Blog Image';
            img.className = 'blog-photo';
            
            fetch(`actions/count-files.php?blog_id=${table.blog_id}`)
                .then(response => response.json())
                .then(data => {
                    const fileCount = data.fileCount;
                    if (fileCount > 1) {
                        leftArrow.style.display = 'inline';
                        rightArrow.style.display = 'inline';
                    }

                    let currentImageIndex = 0;

                    // Left arrow click event
                    leftArrow.addEventListener('click', () => {
                        currentImageIndex--;
                        if (currentImageIndex < 0) {
                            currentImageIndex = fileCount - 1;
                            img.src = `${images.dir}${images.img_names[currentImageIndex]}`;
                        } else if (currentImageIndex == 0) {
                            img.src = `${images.dir}${images.img_names[0]}`;
                        } else {
                            img.src = `${images.dir}${images.img_names[currentImageIndex]}`;
                        }
                    });

                    // Right arrow click event
                    rightArrow.addEventListener('click', () => {
                        currentImageIndex++;
                        if (currentImageIndex == fileCount) {
                            currentImageIndex = 0; // Reset to zero if it reaches fileCount
                            img.src = `${images.dir}${images.img_names[0]}`;
                        } else {
                            img.src = `${images.dir}${images.img_names[currentImageIndex]}`;
                        }
                    });
                })
                .catch(error => console.error('Error fetching file count:', error));

            const blogDescription = document.createElement('p');
            blogDescription.className = 'blog-description';
            blogDescription.textContent = table.description;

            const blogSeparator = document.createElement('hr');
            blogSeparator.className = 'blog-separator';

            blogUserContainer.appendChild(userImage);
            blogUserContainer.appendChild(username);
            blogUserContainer.appendChild(creationDate);

            imageContainer.appendChild(leftArrow);
            imageContainer.appendChild(img);
            imageContainer.appendChild(rightArrow);

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

            blogContainer.appendChild(blogUserContainer);
            blogContainer.appendChild(blogTitle);

            if (img_src !== 'images/photoABCDLogo.png') {
                blogContainer.appendChild(imageContainer);
            }

            if (videoUrl === null) {
                blogContainer.appendChild(imageContainer);
            }
            
            blogContainer.appendChild(videoContainer);

            blogContainer.appendChild(blogDescription);
            postsContainer.appendChild(blogContainer);
            postsContainer.appendChild(blogSeparator);
        });
    }

    // Function to display photo-only view
    function displaySortedBlogs(sortOrder = 'asc', blogModular, blogPosts) {
        blogRow.innerHTML = ''; // Clear the container
        let combinedGet = [];
        // Sort blogs based on title in ascending or descending order
        for (let j = 0; j < blogPosts.length; j++) {
            for (let i = 0; i < blogModular.length; i++) {
                if (blogPosts[j].blog_id === blogModular[i].table.blog_id) {
                    combinedGet.push(blogModular[i])
                    break;
                }
            }
        }
        combinedGet.sort((a, b) => {
            const titleA = a.table.title.toLowerCase();
            const titleB = b.table.title.toLowerCase();
            const dateA = a.table.event_date;
            const dateB = b.table.event_date;
            if (sortOrder === 'asc') {
                return titleA < titleB ? -1 : (titleA > titleB ? 1 : 0);
            } else if (sortOrder === 'desc') {
                return titleA > titleB ? -1 : (titleA < titleB ? 1 : 0);
            } else if (sortOrder === 'date_asc') {
                return dateA < dateB ? -1 : (dateA > dateB ? 1 : 0);
            } else {
                return dateA > dateB ? -1 : (dateA < dateB ? 1 : 0);
            }
        });

        combinedGet.forEach(pair => {
            // Blog Row Attributes
            const table = pair.table;
            const blog_id = table.blog_id;
            const email = table.creator_email;
            const title = table.title;

            const description = table.description;
            const event_date = table.event_date;
            const creation_date = table.creation_date;
            const modification_date = table.modification_date;
            const privacy_filter = table.privacy_filter;

            // Image Array
            const images = pair.images;
            var img_src;

            // define path for default image if none is uploaded
            if (images.img_names.length === 0) {
                img_src = 'images/photoABCDLogo.png';
            } else {
                img_src = `${images.dir}${images.img_names[0]}`;
            }



            createCard(blogRow, title, email, img_src, blog_id, pair);
        });
    }

    // Function to display photo-only grid view (no interaction)
    function displayProfileBlogs(sortOrder = 'asc', blogModular, blogPosts) {
        const profileBlogRow = document.getElementById('profilePostsContainer');
        profileBlogRow.innerHTML = ''; // Clear the container
        let combinedGet = [];

        // Sort blogs based on title or date
        for (let j = 0; j < blogPosts.length; j++) {
            for (let i = 0; i < blogModular.length; i++) {
                if (blogPosts[j].blog_id === blogModular[i].table.blog_id) {
                    combinedGet.push(blogModular[i]);
                    break;
                }
            }
        }

        combinedGet.sort((a, b) => {
            const titleA = a.table.title.toLowerCase();
            const titleB = b.table.title.toLowerCase();
            const dateA = a.table.event_date;
            const dateB = b.table.event_date;
            if (sortOrder === 'asc') {
                return titleA < titleB ? -1 : (titleA > titleB ? 1 : 0);
            } else if (sortOrder === 'desc') {
                return titleA > titleB ? -1 : (titleA < titleB ? 1 : 0);
            } else if (sortOrder === 'date_asc') {
                return dateA < dateB ? -1 : (dateA > dateB ? 1 : 0);
            } else {
                return dateA > dateB ? -1 : (dateA < dateB ? 1 : 0);
            }
        });

        // Iterate through sorted blogs and create the grid view
        combinedGet.forEach(pair => {
            const profileTable = pair.table;
            const blog_id = profileTable.blog_id;
            const profileEmail = profileTable.creator_email;
            const profileTitle = profileTable.title;
            const profileDescription = profileTable.description;
            const profileEvent_date = profileTable.event_date;
            const profileCreation_date = profileTable.creation_date;
            const profileModification_date = profileTable.modification_date;
            const profilePrivacy_filter = profileTable.privacy_filter;

            // Image Array
            const images = pair.images;
            let img_src = images.img_names.length === 0 ? 'images/photoABCDLogo.png' :
                `${images.dir}${images.img_names[0]}`;

            // Create profile cards in grid format
            createCard(profileBlogRow, profileTitle, profileEmail, img_src, blog_id, pair);
        });
    }

    function createCard(container, title, email, img, id, pair) {
        const card = document.createElement("div");
        card.className = 'card';
        card.id = `blog-${id}`;

        // Header
        const cardHeader = document.createElement("div");
        cardHeader.className = "card-header";
        cardHeader.setAttribute('data-bs-target', "#card-modal");
        cardHeader.setAttribute('data-bs-toggle', "modal");
        cardHeader.onclick = function() {
            fillModalPV(pair);
        };

        const cardTitle = document.createElement("h4");
        cardTitle.className = "card-title";
        cardTitle.textContent = title;
        cardHeader.appendChild(cardTitle);

        // Body
        const cardBody = document.createElement("div");
        cardBody.className = "card-body";
        cardBody.setAttribute('data-bs-target', "#card-modal");
        cardBody.setAttribute('data-bs-toggle', "modal");
        cardBody.onclick = function() {
            fillModalPV(pair);
        };

        const cardImage = document.createElement("img");
        cardImage.className = "card-img";
        cardImage.src = img;
        cardBody.appendChild(cardImage);

        // Footer
        const cardFooter = document.createElement("div");
        cardFooter.className = "card-footer";

        const cardEmail = document.createElement("p");
        cardEmail.className = "card-text";
        cardEmail.textContent = email;
        cardFooter.appendChild(cardEmail);

        cardFooter.addEventListener('click', () => {
            $('#viewProfileModal').modal('show');
            loadProfileView(email);
        });

        // Assemble card
        card.appendChild(cardHeader);
        card.appendChild(cardBody);
        card.appendChild(cardFooter);

        // Attach to container
        container.appendChild(card);

        // Style container for layout
        container.style.display = 'flex';
        container.style.flexWrap = 'wrap'; // Allows wrapping of cards
        container.style.justifyContent = 'center'; // Centers cards horizontally
        container.style.alignItems = 'center'; // Centers cards vertically
    }

    function fillModalPV(pair) {
        const table = pair.table;
        const blog_id = table.blog_id;
        const email = table.creator_email;
        const title = table.title;
        const description = table.description;
        const event_date = table.event_date;
        const creation_date = table.creation_date;
        const modification_date = table.modification_date;
        const privacy_filter = table.privacy_filter;

        // Image Array
        const images = pair.images;

        // Image Source
        var img_src;

        // define path for default image if none is uploaded
        if (images.img_names.length === 0) {
            img_src = 'images/photoABCDLogo.png';
        } else {
            img_src = `${images.dir}${images.img_names[0]}`;
        }

        document.getElementById('card-modal-title').innerHTML = title;
        document.getElementById('card-modal-img').setAttribute('src', img_src);
        document.getElementById('card-modal-desc').innerHTML = description;
        document.getElementById('card-modal-email').innerHTML = email;

        // Find a way to work an index with this.
        // Assuming "Previous" and "Next" are part of the pagination component
        const pagenav = document.getElementById('card-modal-img-nav');
        const pageLinks = pagenav.querySelectorAll('.page-link');
        let previousButton, nextButton, indexDisplay;

        // Find "Previous" and "Next" buttons by their text content
        pageLinks.forEach(link => {
            if (link.textContent.trim() === "Previous") {
                previousButton = link;
            } else if (link.textContent.trim() === "Next") {
                nextButton = link;
            } else {
                indexDisplay = link;
            }
        });

        // Hide pagination if there is only one image
        if (images.length > 1) {
            // Show pagination if there are multiple images
            previousButton.style.display = 'inline';
            nextButton.style.display = 'inline';
            indexDisplay.style.display = 'inline';
        } else {
            // Hide pagination if there is only one image
            previousButton.style.display = 'none';
            nextButton.style.display = 'none';
            indexDisplay.style.display = 'none';
        }

        let currentImageIndex = 0;

        // Fetch file count and initialize arrows
        fetch(`actions/count-files.php?blog_id=${table.blog_id}`)
            .then(response => response.json())
            .then(data => {
                const fileCount = data.fileCount;

                if (fileCount > 1) {
                    previousButton.style.display = 'inline';
                    nextButton.style.display = 'inline';
                    indexDisplay.style.display = 'inline';
                }

                // "Previous" button click event
                previousButton.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevent default link behavior
                    if (images.img_names.length > 1) {
                        currentImageIndex--;
                        if (currentImageIndex < 0) {
                            currentImageIndex = fileCount - 1; // Loop back to last image
                        }
                        img_src = `${images.dir}${images.img_names[currentImageIndex]}`;
                    }
                    document.getElementById('card-modal-img').setAttribute('src', img_src);
                    indexDisplay.textContent = currentImageIndex + 1;
                });

                // "Next" button click event
                nextButton.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevent default link behavior
                    if (images.img_names.length > 1) {
                        currentImageIndex++;
                        if (currentImageIndex >= fileCount) {
                            currentImageIndex = 0; // Loop back to first image
                        }
                        img_src = `${images.dir}${images.img_names[currentImageIndex]}`;
                    }
                    document.getElementById('card-modal-img').setAttribute('src', img_src);
                    indexDisplay.textContent = currentImageIndex + 1;
                });
            })
            .catch(error => console.error('Error fetching file count:', error));
        indexDisplay.textContent = 1;
    }

    // Event listeners updated to include viewOptions
    document.getElementById('searchButton').addEventListener('click', () => {
        const title = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const sortOrder = document.getElementById('sortOrder').value;
        const viewOptions = document.getElementById('viewOptions').value;
        fetchBlogs(title, startDate, endDate, sortOrder, viewOptions);
    });

    document.getElementById('searchInput').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const title = document.getElementById('searchInput').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const sortOrder = document.getElementById('sortOrder').value;
            const viewOptions = document.getElementById('viewOptions').value;
            fetchBlogs(title, startDate, endDate, sortOrder, viewOptions);
        }
    });

    document.getElementById('sortOrder').addEventListener('change', () => {
        const title = document.getElementById('searchInput').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const sortOrder = document.getElementById('sortOrder').value;
        const viewOptions = document.getElementById('viewOptions').value;
        fetchBlogs(title, startDate, endDate, sortOrder, viewOptions);
    });

    // Event listeners for profile blogs
    document.getElementById('profileSearchButton').addEventListener('click', () => {
        const profileTitle = document.getElementById('profileSearchInput').value;
        const profileStartDate = document.getElementById('profileStartDate').value;
        const profileEndDate = document.getElementById('profileEndDate').value;
        const profileSortOrder = document.getElementById('profileSortOrder').value;
        fetchProfileBlogs(profileTitle, profileStartDate, profileEndDate, profileSortOrder, creatorId);
    });

    document.getElementById('profileSearchInput').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const profileTitle = document.getElementById('profileSearchInput').value;
            const profileStartDate = document.getElementById('profileStartDate').value;
            const profileEndDate = document.getElementById('profileEndDate').value;
            const profileSortOrder = document.getElementById('profileSortOrder').value;
            fetchProfileBlogs(profileTitle, profileStartDate, profileEndDate, profileSortOrder, creatorId);
        }
    });

    document.getElementById('profileSortOrder').addEventListener('change', () => {
        const profileTitle = document.getElementById('profileSearchInput').value;
        const profileStartDate = document.getElementById('profileStartDate').value;
        const profileEndDate = document.getElementById('profileEndDate').value;
        const profileSortOrder = document.getElementById('profileSortOrder').value;
        fetchProfileBlogs(profileTitle, profileStartDate, profileEndDate, profileSortOrder, creatorId);
    });

    // Initialize the correct view on page load
    const initialViewOptions = document.getElementById('viewOptions').value;
    if (initialViewOptions === 'photoOnly') {
        loadPhotoOnlyView();
    } else {
        loadTraditionalView();
    }
    </script>
</div>
