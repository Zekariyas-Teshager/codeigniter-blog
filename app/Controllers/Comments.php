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
            // Auto-approve comments for admins and authors
            if (is_admin() || is_author()) {
                $this->commentModel->update($this->commentModel->getInsertID(), ['is_approved' => true]);
            }
            
            session()->setFlashdata('success', 'Comment added successfully!' . (!is_admin() && !is_author() ? ' It will be visible after approval.' : ''));
        } else {
            session()->setFlashdata('error', 'Failed to add comment. Please try again.');
        }

        return redirect()->to('post/' . $post['slug']);
    }

    public function approve($id)
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to approve comments.');
        }

        $comment = $this->commentModel->find($id);
        if (!$comment) {
            return redirect()->back()->with('error', 'Comment not found.');
        }

        if ($this->commentModel->update($id, ['is_approved' => true])) {
            session()->setFlashdata('success', 'Comment approved successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to approve comment.');
        }

        return redirect()->back();
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

    public function manage()
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to access this page.');
        }

        $data['title'] = 'Manage Comments';
        $data['unapprovedComments'] = $this->commentModel->getUnapprovedComments();
        $data['recentComments'] = $this->commentModel->orderBy('created_at', 'DESC')->findAll(10);

        return $this->renderView('admin/comments', $data);
    }
}