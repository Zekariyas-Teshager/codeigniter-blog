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

    <!-- <?php if (can_edit_post($post['author_id'])) : ?>
        <section class="mb-5">
            <div class="d-flex gap-2">
                <?php if (can_edit_post($post['author_id'])) : ?>
                    <a href="<?  ?>= base_url('posts/edit/' . $post['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Post
                    </a>
                <?php endif; ?>
                <form action="<?  ?>= base_url('posts/delete/' . $post['id']) ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this post?');">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete Post
                    </button>
                </form>
            </div>
        </section>
    <?php endif;
    ?> -->

    <!-- Comments Section -->
    <section class="mb-5">
        <div class="card bg-light">
            <div class="card-body">
                <h4 class="card-title">Comments</h4>

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
                            <div class="ms-3">
                                <div class="fw-bold"><?= esc($comment['author_name']) ?></div>
                                <small class="text-muted"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></small>
                                <p class="mb-1"><?= nl2br(esc($comment['content'])) ?></p>
                            </div>
                            <!-- Show reply count -->
                            <div class="reply-info">
                                <?php if ($comment['reply_count'] >= 0): ?>
                                    <span class="badge bg-secondary">
                                        <?= $comment['reply_count']; ?>
                                        <?= $comment['reply_count'] == 1 ? 'reply' : 'replies'; ?>
                                    </span>
                                <?php endif; ?>

                                <button class="btn-reply" data-comment-id="<?= $comment['id']; ?>">
                                    Reply
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (is_logged_in()): ?>
                    <hr>
                    <h5>Add a Comment</h5>
                    <form action="<?= base_url('comments/add') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="3" placeholder="Join the discussion..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <a href="<?= base_url('login') ?>" class="alert-link">Login</a> to leave a comment.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</article>