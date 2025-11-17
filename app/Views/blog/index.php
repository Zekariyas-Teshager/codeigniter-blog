<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>All Blog Posts</h1>
            <?php if (can_create_post()): ?>
                <a href="<?= base_url('posts/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Write New Post
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <h4>No posts yet</h4>
                <p class="mb-0">Be the first to write a blog post!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="card post-card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">
                            <a href="<?= base_url('post/' . $post['slug']) ?>" class="text-decoration-none">
                                <?= esc($post['title']) ?>
                            </a>
                        </h2>
                        
                        <div class="text-muted mb-3">
                            <small>
                                <i class="fas fa-user"></i> By <?= esc($post['author_name']) ?> |
                                <i class="fas fa-folder"></i> 
                                <a href="<?= base_url('category/' . $post['category_slug']) ?>" class="text-muted">
                                    <?= esc($post['category_name']) ?>
                                </a> |
                                <i class="fas fa-calendar"></i> 
                                <?= date('M j, Y', strtotime($post['published_at'])) ?>
                            </small>
                        </div>
                        
                        <?php if ($post['excerpt']): ?>
                            <p class="card-text"><?= esc($post['excerpt']) ?></p>
                        <?php else: ?>
                            <p class="card-text"><?= character_limiter(strip_tags($post['content']), 200) ?></p>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('post/' . $post['slug']) ?>" class="btn btn-outline-primary btn-sm">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Categories</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($categories as $category): ?>
                        <a href="<?= base_url('category/' . $category['slug']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <?= esc($category['name']) ?>
                            <span class="badge bg-primary rounded-pill"><?= $category['post_count'] ?? 0 ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>