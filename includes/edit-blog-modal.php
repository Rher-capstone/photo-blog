<div id="editBlogModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBlogForm">
                <div class="modal-header">
                    <h3>Edit Blog</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: bold;">Blog ID</label>
                        <div name="blogId" id="editBlogId" class="form-control-plaintext">
                            <!-- This will be populated with the blog_id value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Creator Email</label>
                        <div id="editCreatorEmail" class="form-control-plaintext">
                            <!-- This will be populated with the creator_email value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Title</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Description</label>
                        <input type="text" name="description" id="editDescription" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Youtube Link</label>
                        <input type="text" name="youtubeLink" id="editYoutubeLink" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Event Date</label>
                        <input type="datetime-local" name="eventDate" id="editEventDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Creation Date</label>
                        <input type="datetime-local" name="creationDate" id="editCreationDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Modification Date</label>
                        <input type="datetime-local" name="modificationDate" id="editModificationDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;" for="privacyFilter">Privacy Filter</label>
                        <select name="privacyFilter" id="editPrivacyFilter" class="form-control" required>
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveBlogChanges()">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



