<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $postModel;
    protected $commentModel;
    protected $userModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->commentModel = new CommentModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $userId = user_data('id');
        $userRole = user_data('role');

        // Different data based on user role
        if ($userRole === 'admin') {
            $data['recent_posts'] = $this->postModel->orderBy('created_at', 'DESC')->findAll(5);
            $data['pending_comments'] = $this->commentModel->getUnapprovedComments(5);
            $data['total_pending_comments'] = $this->commentModel->where('is_approved', false)->countAllResults();
            $data['total_posts'] = $this->postModel->countAll();
            $data['total_comments'] = $this->commentModel->countAll();
            $data['total_users'] = $this->userModel->countAll();
        } elseif ($userRole === 'author') {
            $data['recent_posts'] = $this->postModel->where('author_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll(5);
            $data['published_posts'] = $this->postModel->where('author_id', $userId)
                ->where('status', 'published')
                ->countAllResults();
            $data['draft_posts'] = $this->postModel->where('author_id', $userId)
                ->where('status', 'draft')
                ->countAllResults();
        }

        return $this->renderView('dashboard/index', $data);
    }

    public function profile()
    {
        $data['title'] = 'My Profile';
        $userId = user_data('id');

        if ($this->request->getMethod() === 'POST') {
            $updateData = [
                'username' => $this->request->getPost('username') !== user_data('username') ?? $this->request->getPost('username'),
                'email' => $this->request->getPost('email') !== user_data('email') ?? $this->request->getPost('email')
            ];

            // Check if password is being updated
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $old_password = $this->request->getPost('old_password');
                $user = $this->userModel->find($userId);
                if (!$this->userModel->verifyPassword($old_password, $user['password'])) {
                    return redirect()->back()->with('error', 'Old password is incorrect.')->withInput();
                }
                $updateData['password'] = $password;
            }

            $rules = [
                'username' => "min_length[3]|max_length[50]|is_unique[users.username,id,{$userId}]",
                'email' => "valid_email|is_unique[users.email,id,{$userId}]"
            ];

            if (!empty($password)) {
                $rules['password'] = 'min_length[8]';
                $rules['password_confirm'] = 'matches[password]';
            }

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                $userModel = new \App\Models\UserModel();
                if ($userModel->update($userId, $updateData)) {
                    // Update session data
                    $user = $userModel->find($userId);
                    $auth = service('authentication');

                    // Use reflection to access private method or recreate session
                    $sessionData = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'profile_image' => $user['profile_image'],
                        'is_active' => $user['is_active'],
                        'logged_in' => true
                    ];

                    session()->set('auth_user', $sessionData);

                    session()->setFlashdata('success', 'Profile updated successfully!');
                    return redirect()->to('dashboard/profile');
                } else {
                    // Get database errors
                    $errors = $this->postModel->errors();

                    // Show specific error message or generic one
                    if (!empty($errors)) {
                        $errorMessage = 'Failed to update post: ' . implode(', ', $errors);
                    } else {
                        $errorMessage = 'Failed to update post. Please try again.';
                    }

                    session()->setFlashdata('error', $errorMessage);
                }
            }
        }

        return $this->renderView('dashboard/profile', $data);
    }
}
