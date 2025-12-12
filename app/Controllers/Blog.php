<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\CategoryModel;
use App\Models\CommentModel;

class Blog extends BaseController
{
    protected $postModel;
    protected $categoryModel;
    protected $commentModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->categoryModel = new CategoryModel();
        $this->commentModel = new CommentModel();
    }

    public function index()
    {
        $data['title'] = 'All Posts';

        // Get all published posts
        $data['posts'] = $this->postModel->getPublishedPosts(10);
        $data['categories'] = $this->categoryModel->getCategoriesWithPostCount();

        return $this->renderView('blog/index', $data);
    }

    public function view($slug)
    {
        $post = $this->postModel->getPostBySlug($slug, user_data('id'));

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $post['title'],
            'post' => $post,
            'comments' => $this->commentModel->getCommentsByPost($post['id'])
        ];

        return $this->renderView('blog/view', $data);
    }

    public function category($slug)
    {
        $category = $this->categoryModel->getCategoryBySlug($slug);

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Category: ' . $category['name'],
            'category' => $category,
            'posts' => $this->postModel->getPostsByCategory($category['id']),
            'categories' => $this->categoryModel->getCategoriesWithPostCount(),
        ];

        return $this->renderView('blog/category', $data);
    }

    public function create()
    {
        // Check if user can create posts
        if (!can_create_post()) {
            return redirect()->back()->with('error', 'You do not have permission to create posts.');
        }

        $data['title'] = 'Create New Post';
        $data['categories'] = $this->categoryModel->findAll();

        if ($this->request->getMethod() === 'POST') {
            $postData = [
                'title' => $this->request->getPost('title'),
                'slug' => url_title($this->request->getPost('title'), '-', true),
                'content' => $this->request->getPost('content'),
                'excerpt' => $this->request->getPost('excerpt'),
                'category_id' => $this->request->getPost('category_id'),
                'author_id' => user_data('id'),
                'status' => $this->request->getPost('status')
            ];

            $rules = [
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[10]',
                'category_id' => 'required|integer'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if ($this->postModel->insert($postData)) {
                    session()->setFlashdata('success', 'Post created successfully!');
                    return redirect()->to('posts/my-posts');
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

        return $this->renderView('blog/create', $data);
    }

    public function edit($id)
    {
        $post = $this->postModel->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if user can edit this post
        if (!can_edit_post($post['author_id'])) {
            return redirect()->back()->with('error', 'You do not have permission to edit this post.');
        }

        $data = [
            'title' => 'Edit Post',
            'post' => $post,
            'categories' => $this->categoryModel->findAll()
        ];

        if ($this->request->getMethod() === 'POST') {
            $postData = [
                'title' => $this->request->getPost('title'),
                'slug' => $this->request->getPost('title') !== $post['title'] ?? url_title($this->request->getPost('title'), '-', true),
                'content' => $this->request->getPost('content'),
                'excerpt' => $this->request->getPost('excerpt'),
                'category_id' => $this->request->getPost('category_id'),
                'status' => $this->request->getPost('status')
            ];

            $rules = [
                'title' => 'required|min_length[5]|max_length[255]',
                'content' => 'required|min_length[10]',
                'category_id' => 'required|integer'
            ];

            if (!$this->validate($rules)) {
                $data['validation'] = $this->validator;
            } else {
                if ($this->postModel->update($id, $postData)) {
                    session()->setFlashdata('success', 'Post updated successfully!');
                    return redirect()->to('posts/my-posts');
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

        return $this->renderView('blog/edit', $data);
    }

    public function delete($id)
    {
        $post = $this->postModel->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if user can delete this post
        if (!can_delete_post($post['author_id'])) {
            return redirect()->back()->with('error', 'You do not have permission to delete this post.');
        }

        if ($this->postModel->delete($id)) {
            session()->setFlashdata('success', 'Post deleted successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to delete post.');
        }

        return redirect()->to('posts/my-posts');
    }

    public function myPosts()
    {
        if (!can_create_post()) {
            return redirect()->back()->with('error', 'You do not have permission to view this page.');
        }

        $status = $this->request->getGet('s');

        if (!empty($status)) {
            $data['title'] = 'My Posts: ' . $status;
            $data['posts'] = $this->postModel
                ->select('posts.*, categories.name as category_name, categories.slug as category_slug')
                ->join('categories', 'categories.id = posts.category_id')
                ->where('author_id', user_data('id'))
                ->where('status', $status)
                ->findAll();
            $data['results_count'] = count($data['posts']);
            $data['status'] = ucwords($status);
            return $this->renderView('blog/my_posts', $data);
        }

        $data['title'] = 'My Posts';
        $data['posts'] = $this->postModel->getPostsByAuthor(user_data('id'));

        return $this->renderView('blog/my_posts', $data);
    }

    public function search()
    {
        $query = $this->request->getGet('q');

        if (empty($query)) {
            return redirect()->to('posts');
        }

        $data['title'] = 'Search Results: ' . $query;
        $data['search_query'] = $query;
        $data['posts'] = $this->postModel->searchPosts($query);
        $data['results_count'] = count($data['posts']);

        return $this->renderView('blog/search', $data);
    }
}
