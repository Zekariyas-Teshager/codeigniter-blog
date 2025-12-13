<?php

namespace App\Controllers;

use App\Models\CommentModel;
use App\Models\PostModel;

class Comments extends BaseController
{
    protected $commentModel;
    protected $postModel;

    public function __construct()
    {
        $this->commentModel = new CommentModel();
        $this->postModel = new PostModel();
    }

    public function add()
    {
        if (!$this->request->is('POST')) {
            return redirect()->back()->with('error', 'Invalid request method.');
        }

        if (!is_logged_in()) {
            return redirect()->to('login')->with('error', 'Please login to comment.');
        }

        $postId = $this->request->getPost('post_id');
        $content = $this->request->getPost('content');

        // Check if post exists
        $post = $this->postModel->find($postId);
        if (!$post) {
            return redirect()->back()->with('error', 'Post not found.');
        }

        $commentData = [
            'post_id' => $postId,
            'user_id' => user_data('id'),
            'content' => $content
        ];

        $rules = [
            'post_id' => 'required|integer',
            'content' => 'required|min_length[5]|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Comment must be between 5 and 1000 characters.')->withInput();
        }

        if ($this->commentModel->insert($commentData)) {
            session()->setFlashdata('success', 'Comment added successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to add comment. Please try again.');
        }

        return redirect()->to('post/' . $post['slug']);
    }

    public function addReply()
    {
        if (!$this->request->is('POST')) {
            return redirect()->back()->with('error', 'Invalid request method.');
        }

        if (!is_logged_in()) {
            return redirect()->to('login')->with('error', 'Please login to reply.');
        }

        $postId = $this->request->getPost('post_id');
        $parentId = $this->request->getPost('parent_id');
        $content = $this->request->getPost('content');

        // Check if post exists
        $post = $this->postModel->find($postId);
        if (!$post) {
            return redirect()->back()->with('error', 'Post not found.');
        }

        $replyData = [
            'post_id' => $postId,
            'user_id' => user_data('id'),
            'parent_id' => $parentId,
            'content' => $content
        ];

        $rules = [
            'post_id' => 'required|integer',
            'parent_id' => 'required|integer',
            'content' => 'required|min_length[5]|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Reply must be between 5 and 1000 characters.')->withInput();
        }

        if ($this->commentModel->insert($replyData)) {
            session()->setFlashdata('success', 'Reply added successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to add reply. Please try again.');
        }

        return redirect()->to('post/' . $post['slug']);
    }

    public function loadReplies($commentId)
    {
        // Check if it's an AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $page = $this->request->getGet('page') ?: 1;
        $limit = $this->request->getGet('limit') ?: 5;

        $replies = $this->commentModel->getReplies($commentId, $page, $limit);
        $totalReplies = $this->commentModel->getTotalReplies($commentId);
        $totalPages = ceil($totalReplies / $limit);

        // Generate HTML for replies
        $html = '';
        foreach ($replies as $reply) {
            $html .= $this->getReplyHtml($reply);
        }

        // Add load more button if there are more replies
        $loadMoreHtml = '';
        if ($page < $totalPages) {
            $loadMoreHtml = '
        <div class="load-more-container text-center mt-3">
            <button class="btn btn-sm btn-outline-primary load-more-replies" 
                    data-comment-id="' . $commentId . '" 
                    data-page="' . ($page + 1) . '">
                <i class="fas fa-chevron-down me-1"></i>
                Load More Replies (' . ($totalReplies - ($page * $limit)) . ' more)
            </button>
        </div>';
        }

        return $this->response->setJSON([
            'success' => true,
            'html' => $html,
            'load_more_html' => $loadMoreHtml,
            'total_replies' => $totalReplies,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ]);
    }

    private function getReplyHtml($reply)
    {
        // Safely get author name with fallback
        $authorName = $reply['author_name'] ?? 'Anonymous';

        // Get first letter for avatar
        $firstLetter = strtoupper(substr($authorName, 0, 1));

        // Build avatar HTML using first letter
        $avatarHtml = '
    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
         style="width:32px;height:32px;font-size:0.8rem;">
        ' . $firstLetter . '
    </div>';

        // Safely get and format date
        $date = 'Recent';
        if (!empty($reply['created_at'])) {
            try {
                $date = date('M j, Y g:i a', strtotime($reply['created_at']));
            } catch (\Exception $e) {
                // Keep default if date parsing fails
            }
        }

        // Safely get and escape content
        $content = $reply['content'] ?? '';
        $escapedContent = nl2br(htmlspecialchars($content));

        return '
            <div class="reply-item mb-3" id="reply-' . ($reply['id'] ?? '') . '">
                <div class="card border-start border-primary">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center mb-2">
                            ' . $avatarHtml . '
                            <div>
                                <h6 class="mb-0" style="font-size:0.9rem;">
                                    ' . htmlspecialchars($authorName) . '
                                </h6>
                                <small class="text-muted">
                                    ' . $date . '
                                </small>
                            </div>
                        </div>
                        <p class="mb-0" style="font-size:0.95rem;">
                            ' . $escapedContent . '
                        </p>
                    </div>
                </div>
            </div>';
    }

    public function delete($id)
    {
        $comment = $this->commentModel->find($id);
        if (!$comment) {
            return redirect()->back()->with('error', 'Comment not found.');
        }

        // Check if user can delete this comment
        $canDelete = is_admin() || (is_logged_in() && user_data('id') == $comment['user_id']);

        if (!$canDelete) {
            return redirect()->back()->with('error', 'You do not have permission to delete this comment.');
        }

        if ($this->commentModel->delete($id)) {
            session()->setFlashdata('success', 'Comment deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete comment.');
        }

        return redirect()->back();
    }
}
