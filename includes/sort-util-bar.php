<div class="sorting-components">
    <!-- Search Form -->
    <div class="wrapper" id="searchContainer">

        <div class="labeled-input">
            <label for="startDate">Title:</label>
            <input type="text" id="searchInput" placeholder="Search by title...">
        </div>

        <div class="labeled-input">
            <label for="startDate">From:</label>
            <input type="date" id="startDate" placeholder="Start Date">
        </div>

        <div class="labeled-input">
            <label for="endDate">To:</label>
            <input type="date" id="endDate" placeholder="End Date">
        </div>

        <div class="labeled-input">
            <button class="sort-util-button" id="searchButton">Search</button>
        </div>

        <div class="labeled-input">
            <button class="sort-util-button" id="clearButton" onclick="resetSearch()">Clear</button>
        </div>

    </div>

    <!-- Search Form -->
    <div class="sorting-dropdown wrapper">

        <div class="labeled-input" id="sortContainer">
            <label for="sortOrder">Sort Order:</label>
            <select id="sortOrder">
                <option value="asc">Alphabetically (Ascending)</option>
                <option value="desc">Alphabetically (Descending)</option>
                <option value="date_asc">Event Date (Ascending)</option>
                <option value="date_des">Event Date (Descending)</option>
            </select>
        </div>

        <!-- View Option Container -->
        <div class="labeled-input" id="viewOptionContainer">
            <label for="viewOptions">View:</label>
            <select id="viewOptions">
                <option value="traditional">Traditional</option>
                <option value="photoOnly">Photo Only</option>
            </select>
        </div>

    </div>

    <div class="wrapper" id="blogFunctionsContainer">
        <div class="labeled-input">
            <label for="newBlogButton">Blogs:</label>
            <button class="sort-util-button" id="newBlogButton" 
                data-bs-toggle="modal" data-bs-target="#newBlogModal">
                New Blog
            </button>
        </div>

        <div class="labeled-input">
            <button class="sort-util-button" id="printBlogsButton">Print Blogs</button>
        </div>

        <!-- Add Privacy Button -->
        <div class="labeled-input">
            <button class="sort-util-button" id="privacyButton">Privacy</button>
        </div>
    </div>
</div>

<script>
    function resetSearch() 
    {
        document.getElementById("searchInput").value = "";
        document.getElementById("startDate").value = null;
        document.getElementById("endDate").value = null;
    }
</script>