<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['name', 'slug', 'description'];
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
        'slug' => 'required|min_length[3]|max_length[100]|is_unique[categories.slug]',
    ];
    
    public function getCategoriesWithPostCount()
    {
        $builder = $this->db->table('categories');
        $builder->select('categories.*, COUNT(posts.id) as post_count');
        $builder->join('posts', 'posts.category_id = categories.id AND posts.status = "published"', 'left');
        $builder->groupBy('categories.id');
        
        return $builder->get()->getResultArray();
    }
    
    public function getCategoryBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }
}