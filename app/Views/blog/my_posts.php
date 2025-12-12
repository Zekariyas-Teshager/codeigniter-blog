<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>My Posts</h1>
            <a href="<?= base_url('posts/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Write New Post
            </a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h3><?= 'No ' .esc($status?? ''). ' Posts.' ?></h3>
                    <p class="text-muted"><?= "You don't have any " .esc($status?? ''). ' blog posts.' ?></p>
                    <a href="<?= base_url('posts/create') ?>" class="btn btn-primary">Create Your First Post</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($post['title']) ?></strong>
                                        </td>
                                        <td><?= esc($post['category_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $post['status'] === 'published' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($post['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('post/' . $post['slug']) ?>" class="btn btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="<?= base_url('posts/edit/' . $post['id']) ?>" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="<?= base_url('posts/delete/' . $post['id']) ?>" class="btn btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this post?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>