<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Search Results</h1>
            <a href="<?= base_url('posts') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Posts
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <p class="mb-0">
                    Found <strong><?= $results_count ?></strong> results for: 
                    <strong>"<?= esc($search_query) ?>"</strong>
                </p>
            </div>
        </div>

        <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <h4>No results found</h4>
                <p class="mb-0">Try different keywords or browse all posts.</p>
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
                                <i class="fas fa-folder"></i> <?= esc($post['category_name']) ?> |
                                <i class="fas fa-calendar"></i> 
                                <?= date('M j, Y', strtotime($post['published_at'])) ?>
                            </small>
                        </div>
                        
                        <p class="card-text">
                            <?php 
                            $content = strip_tags($post['content']);
                            echo strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
                            ?>
                        </p>
                        
                        <a href="<?= base_url('post/' . $post['slug']) ?>" class="btn btn-outline-primary btn-sm">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>