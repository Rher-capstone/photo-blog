<div id="deleteBlogModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteBlogForm">
                <div class="modal-header">
                    <h3>Delete Blog</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: bold;">Blog ID</label>
                        <div id="deleteBlogId" class="form-control-plaintext">
                            <!-- This will be populated with the blog_id value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Creator Email</label>
                        <div id="deleteCreatorEmail" class="form-control-plaintext">
                            <!-- This will be populated with the creator_email value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Title</label>
                        <div id="deleteTitle" class="form-control-plaintext">
                            <!-- This will be populated with the title value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Description</label>
                        <div id="deleteDescription" class="form-control-plaintext">
                            <!-- This will be populated with the description value -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: bold;">Would you like to delete this blog?</label>
                        <select type="deleteBlog" name="deleteBlog" id="deleteBlog" class="form-control" required>
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveDeleteBlogChanges()">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
