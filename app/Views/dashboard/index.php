<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Dashboard</h1>
            <?php if (can_create_post()): ?>
                <a href="<?= site_url('posts/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Write New Post
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (is_author()): ?>
    <!-- Author Dashboard -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <a href="<?= site_url('posts/my-posts?s=published') ?>" class="btn btn-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $published_posts ?? 0 ?></h4>
                                <p class="mb-0">Published Posts</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <a href="<?= site_url('posts/my-posts?s=draft') ?>" class="btn btn-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= $draft_posts ?? 0 ?></h4>
                                <p class="mb-0">Draft Posts</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-edit fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <a href="<?= site_url('posts/my-posts')?>" class="btn btn-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?= ($published_posts ?? 0) + ($draft_posts ?? 0) ?></h4>
                                <p class="mb-0">Total Posts</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-newspaper fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Your Recent Posts</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($recent_posts)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recent_posts as $post): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?= esc($post['title']) ?></h6>
                                <small class="text-muted">
                                    Status: <span class="badge bg-<?= $post['status'] === 'published' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($post['status']) ?>
                                    </span> |
                                    <?= date('M j, Y', strtotime($post['created_at'])) ?>
                                </small>
                            </div>
                            <div>
                                <a href="<?= site_url('posts/edit/' . $post['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 text-center">
                    <a href="<?= site_url('posts') ?>" class="btn btn-outline-primary">View All Posts</a>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5>No posts yet</h5>
                    <p class="text-muted">Start writing your first blog post!</p>
                    <a href="<?= site_url('posts/create') ?>" class="btn btn-primary">Write Your First Post</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <!-- Reader Dashboard -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-user fa-3x text-primary mb-3"></i>
            <h3>Welcome to Your Dashboard</h3>
            <p class="text-muted mb-4">As a reader, you can explore blog posts and interact with the community.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= site_url() ?>" class="btn btn-primary">Browse Posts</a>
                <a href="<?= site_url('profile') ?>" class="btn btn-outline-secondary">Update Profile</a>
            </div>
        </div>
    </div>
<?php endif; ?>