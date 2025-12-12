<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Dashboard</h1>
            <?php if (can_create_post()): ?>
                <a href="<?= base_url('posts/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Write New Post
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (is_admin()): ?>
    <!-- Admin Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $stats['total_posts'] ?? 0 ?></h4>
                            <p class="mb-0">Total Posts</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $stats['total_comments'] ?? 0 ?></h4>
                            <p class="mb-0">Total Comments</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $stats['total_pending_comments'] ?? 0 ?></h4>
                            <p class="mb-0">Pending Comments</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $stats['total_users'] ?? 0 ?></h4>
                            <p class="mb-0">Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Posts</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_posts)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_posts as $post): ?>
                                <div class="list-group-item">
                                    <h6 class="mb-1"><?= esc($post['title']) ?></h6>
                                    <small class="text-muted">
                                        By <?= esc($post['author_id']) ?> | 
                                        <?= date('M j, Y', strtotime($post['created_at'])) ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No posts yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pending Comments</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($pending_comments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($pending_comments as $comment): ?>
                                <div class="list-group-item">
                                    <h6 class="mb-1"><?= esc($comment['author_name']) ?></h6>
                                    <p class="mb-1 small">
                                        <?php 
                                        $content = esc($comment['content']);
                                        echo strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                                        ?>
                                    </p>
                                    <small class="text-muted">On: <?= esc($comment['post_title']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No pending comments.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>