<!-- new-blog-modal.php -->
<div class="modal fade" id="newBlogModal" tabindex="-1" aria-labelledby="newBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newBlogModalLabel">Create New Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="new-blog-form" action="actions/new-blog.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="desc" required>
                    </div>
                    <div class="mb-3">
                        <label for="new-blog-images" class="form-label">Upload Images</label>
                        <input type="file" class="form-control" id="new-blog-images" name="new-blog-images[]" accept=".jpg, .jpeg, .png, .gif" multiple>
                    </div>
                    <div class="mb-3">
                        <label for="new-blog-images" class="form-label">Upload Youtube Link</label>
                        <input type="text" class="form-control" id="youtube_link" name="youtube_link">
                    </div>
                    <div class="mb-3">
                        <label for="event-date" class="form-label">Event Date</label>
                        <input type="datetime-local" class="form-control" id="event-date" name="event-date" required>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="create-new-blog" value="Post Blog" class="btn btn-primary">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="visibility" name="visibility">
                            <label class="form-check-label" for="visibility">Public</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>