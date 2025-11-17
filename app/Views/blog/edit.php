<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Post</h4>
            </div>
            <div class="card-body">
                <?php if (isset($validation)): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('posts/edit/' . $post['id']) ?>" method="post">
                     <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= (isset($validation) && $validation->hasError('title')) ? 'is-invalid' : '' ?>" 
                               id="title" name="title" value="<?= old('title', esc($post['title'])) ?>" required>
                        <?php if (isset($validation) && $validation->hasError('title')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('title') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">URL Slug</label>
                        <input type="text" class="form-control" id="slug" value="<?= esc($post['slug']) ?>" readonly>
                        <div class="form-text">Slug is automatically generated from the title and cannot be changed.</div>
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                  placeholder="Brief summary of your post (optional)"><?= old('excerpt', esc($post['excerpt'])) ?></textarea>
                        <div class="form-text">A brief summary that will appear in post listings. If empty, it will be generated from content.</div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= (isset($validation) && $validation->hasError('content')) ? 'is-invalid' : '' ?>" 
                                  id="content" name="content" rows="15" required 
                                  placeholder="Write your post content here..."><?= old('content', esc($post['content'])) ?></textarea>
                        <?php if (isset($validation) && $validation->hasError('content')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('content') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select <?= (isset($validation) && $validation->hasError('category_id')) ? 'is-invalid' : '' ?>" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= (old('category_id', $post['category_id']) == $category['id']) ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('category_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('category_id') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?= (old('status', $post['status']) == 'draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= (old('status', $post['status']) == 'published') ? 'selected' : '' ?>>Published</option>
                                </select>
                                <div class="form-text">
                                    <?php if ($post['status'] === 'published'): ?>
                                        <span class="text-success">
                                            <i class="fas fa-check-circle"></i> This post is currently published and visible to everyone.
                                        </span>
                                    <?php else: ?>
                                        <span class="text-warning">
                                            <i class="fas fa-clock"></i> This post is a draft and only visible to you.
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Created</label>
                                <input type="text" class="form-control" value="<?= date('F j, Y g:i A', strtotime($post['created_at'])) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Last Updated</label>
                                <input type="text" class="form-control" value="<?= date('F j, Y g:i A', strtotime($post['updated_at'])) ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <?php if ($post['status'] === 'published' && !empty($post['published_at'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Published Date</label>
                            <input type="text" class="form-control" value="<?= date('F j, Y g:i A', strtotime($post['published_at'])) ?>" readonly>
                        </div>
                    <?php endif; ?>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Post Preview</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="<?= base_url('post/' . $post['slug']) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-eye"></i> View Live Post
                                </a>
                                <a href="<?= base_url('posts/delete/' . $post['id']) ?>" class="btn btn-outline-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Delete Post
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= base_url('posts/my-posts') ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Back to My Posts
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.post-content {
    line-height: 1.6;
}

.form-label {
    font-weight: 500;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}
</style>

<script>
// Auto-resize textarea for content
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    
    if (contentTextarea) {
        // Set minimum height
        contentTextarea.style.minHeight = '300px';
        
        // Auto-resize as user types
        contentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Trigger initial resize
        contentTextarea.dispatchEvent(new Event('input'));
    }
    
    // Add confirmation for navigation away from page with unsaved changes
    const form = document.querySelector('form');
    let formChanged = false;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            formChanged = true;
        });
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });
    
    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
    
    form.addEventListener('submit', () => {
        formChanged = false;
    });
});
</script>