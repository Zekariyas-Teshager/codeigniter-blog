<article>
    <header class="mb-4">
        <h1 class="fw-bolder mb-1"><?= esc($post['title']) ?></h1>

        <div class="text-muted fst-italic mb-2">
            Posted on <?= date('F j, Y', strtotime($post['published_at'])) ?> by <?= esc($post['author_name']) ?>
        </div>

        <a class="badge bg-secondary text-decoration-none" href="<?= base_url('category/' . $post['category_slug']) ?>">
            <?= esc($post['category_name']) ?>
        </a>
    </header>

    <section class="mb-5">
        <div class="post-content">
            <?= nl2br(esc($post['content'])) ?>
        </div>
    </section>

    <!-- Comments Section -->
    <section class="mb-5">
        <div class="card bg-light">
            <div class="card-body">
                <h4 class="card-title">Comments
                    <span class="badge bg-primary"><?= $totalComments ?? 0 ?></span>
                </h4>

                <?php if (empty($comments)): ?>
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <?= strtoupper(substr($comment['author_name'], 0, 1)) ?>
                                </div>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-bold"><?= esc($comment['author_name']) ?></div>
                                <small class="text-muted"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></small>
                                <p class="mb-1"><?= nl2br(esc($comment['content'])) ?></p>

                                <!-- Reply button -->
                                <button class="btn btn-sm btn-outline-primary reply-to-comment-btn mt-1"
                                    data-comment-id="<?= $comment['id']; ?>"
                                    data-comment-author="<?= htmlspecialchars($comment['author_name']); ?>">
                                    <i class="fas fa-reply me-1"></i>Reply
                                </button>
                                <!-- Show replies button (only if there are replies) -->
                                <?php if ($comment['reply_count'] && $comment['reply_count'] > 0): ?>
                                    <button class="btn btn-sm btn-outline-secondary show-replies-btn ms-1"
                                        data-comment-id="<?= $comment['id']; ?>"
                                        data-loaded="false">
                                        <i class="fas fa-comments me-1"></i>
                                        <?= $comment['reply_count']; ?>
                                        <?= $comment['reply_count'] == 1 ? 'reply' : 'replies'; ?>
                                    </button>
                                <?php endif; ?>

                                <!-- Reply Form (Initially hidden) -->
                                <div class="reply-form-container mt-4" id="reply-form-container-<?= $comment['id']; ?>" style="display: none;">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0" id="reply-form-title">Reply to Comment</h5>
                                            <button type="button" class="btn-close btn-close-white" id="cancel-reply-btn"
                                            data-comment-id="<?= $comment['id']; ?>"></button>
                                        </div>
                                        <div class="card-body">
                                            <form id="reply-form" method="post">
                                                <?= csrf_field(); ?>

                                                <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                                                <input type="hidden" name="parent_id" id="parent_id" value="">

                                                <!-- Reply form (shown only if logged in) -->
                                                <?php if (is_logged_in()): ?>
                                                    <div class="mb-3">
                                                        <label for="reply_content" class="form-label">Your Reply *</label>
                                                        <textarea class="form-control" id="reply_content" name="content"
                                                            rows="3" placeholder="Write your reply here..." required></textarea>
                                                    </div>

                                                    <div class="d-flex justify-content-between">
                                                        <button type="submit" class="btn btn-primary" id="submit-reply-btn">
                                                            <span class="spinner-border spinner-border-sm d-none"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="btn-text">Submit Reply</span>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" id="cancel-reply-form-btn" 
                                                            data-comment-id="<?= $comment['id']; ?>">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-info mt-4">
                                                        <a href="<?= base_url('login') ?>" class="alert-link">Login</a> to leave a comment or reply.
                                                    </div>
                                                <?php endif; ?>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Replies container (initially hidden) -->
                                <div class="replies-container mt-3" id="replies-container-<?= $comment['id']; ?>"
                                    style="display: none;">
                                    <!-- Replies will be loaded here via AJAX -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Comment Form -->
                <?php if (is_logged_in()): ?>
                    <hr class="mt-4">
                    <h5>Add a Comment</h5>
                    <form action="<?= base_url('comments/add') ?>" method="post" id="comment-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" placeholder="Join the discussion..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mt-4">
                        <a href="<?= base_url('login') ?>" class="alert-link">Login</a> to leave a comment or reply.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</article>

<!-- JavaScript for Reply Functionality -->
<script>
    // Define APP_CONFIG before loading comments.js
    window.APP_CONFIG = window.APP_CONFIG || {
        baseUrl: '<?= base_url() ?>',
        currentPostId: <?= $post['id'] ?? 0 ?>
    };
</script>
<script src="<?= base_url('assets/js/comments.js') ?>"></script>

<!-- CSS for Replies -->
<style>
    .replies-container .reply-item {
        margin-left: 3rem;
        border-left: 3px solid #dee2e6;
        padding-left: 1rem;
        margin-top: 1rem;
    }

    .replies-container .reply-item .card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .reply-form-container {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-notification {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .show-replies-btn,
    .reply-to-comment-btn {
        transition: all 0.2s ease;
    }

    .show-replies-btn:hover,
    .reply-to-comment-btn:hover {
        transform: translateY(-1px);
    }
</style>