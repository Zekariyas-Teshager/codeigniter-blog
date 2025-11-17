<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\CategoryModel;

class Home extends BaseController
{
    protected $postModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data['title'] = 'Home';

        // Get published posts with pagination
        $data['posts'] = $this->postModel->getPublishedPosts(10);
        // Get categories with post counts
        $data['categories'] = $this->categoryModel->getCategoriesWithPostCount();
        
        return $this->renderView('home', $data);
    }
}