<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\CategoryModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $postModel;
    protected $commentModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->commentModel = new CommentModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to access the admin panel.');
        }

        $data['title'] = 'Admin Dashboard';
        
        // Statistics
        $data['stats'] = [
            'total_users' => $this->userModel->countAll(),
            'total_posts' => $this->postModel->countAll(),
            'total_comments' => $this->commentModel->countAll(),
            'total_pending_comments' => $this->commentModel->where('is_approved', false)->countAllResults(),
            'total_categories' => $this->categoryModel->countAll()
        ];

        // Recent activity
        $data['recent_posts'] = $this->postModel->orderBy('created_at', 'DESC')->findAll(5);
        $data['recent_users'] = $this->userModel->orderBy('created_at', 'DESC')->findAll(5);
        $data['pending_comments'] = $this->commentModel->getUnapprovedComments(5);


        return $this->renderView('admin/dashboard', $data);
    }

    public function users()
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to manage users.');
        }

        $data['title'] = 'Manage Users';
        $data['users'] = $this->userModel->findAll();

        return $this->renderView('admin/users', $data);
    }

    public function editUser($id)
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to edit users.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];

        if ($this->request->getMethod() === 'post') {
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'role' => $this->request->getPost('role'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            $rules = [
                'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
                'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'role' => 'required|in_list[admin,author,reader]'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if ($this->userModel->update($id, $userData)) {
                    session()->setFlashdata('success', 'User updated successfully!');
                    return redirect()->to('admin/users');
                } else {
                    session()->setFlashdata('error', 'Failed to update user.');
                }
            }
        }

        return $this->renderView('admin/edit_user', $data);
    }

    public function categories()
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to manage categories.');
        }

        $data['title'] = 'Manage Categories';
        $data['categories'] = $this->categoryModel->findAll();

        return $this->renderView('admin/categories', $data);
    }

    public function createCategory()
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to create categories.');
        }

        $data['title'] = 'Create Category';

        if ($this->request->getMethod() === 'post') {
            $categoryData = [
                'name' => $this->request->getPost('name'),
                'slug' => url_title($this->request->getPost('name'), '-', true),
                'description' => $this->request->getPost('description')
            ];

            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
                'description' => 'permit_empty|max_length[500]'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if ($this->categoryModel->insert($categoryData)) {
                    session()->setFlashdata('success', 'Category created successfully!');
                    return redirect()->to('admin/categories');
                } else {
                    session()->setFlashdata('error', 'Failed to create category.');
                }
            }
        }

        return $this->renderView('admin/create_category', $data);
    }

    public function deleteCategory($id)
    {
        if (!is_admin()) {
            return redirect()->back()->with('error', 'You do not have permission to delete categories.');
        }

        // Check if category has posts
        $postsInCategory = $this->postModel->where('category_id', $id)->countAllResults();
        if ($postsInCategory > 0) {
            return redirect()->back()->with('error', "Cannot delete category. There are {$postsInCategory} posts in this category.");
        }

        if ($this->categoryModel->delete($id)) {
            session()->setFlashdata('success', 'Category deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete category.');
        }

        return redirect()->to('admin/categories');
    }
}