<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'author_id',
        'category_id',
        'status',
        'published_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[5]|max_length[255]',
        'slug' => 'is_unique[posts.slug,id,{id}]',
        'content' => 'required|min_length[10]',
        'category_id' => 'required|integer',
        'author_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'slug' => [
            'is_unique' => 'The slug must be unique. Another post with this slug already exists.'
        ],
    ];

    protected $beforeInsert = ['setPublishedAt'];
    protected $beforeUpdate = ['setPublishedAt'];

    protected function setPublishedAt(array $data)
    {
        if (isset($data['data']['status']) && $data['data']['status'] === 'published') {
            $data['data']['published_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    public function getPublishedPosts($limit = null, $offset = 0)
    {
        $builder = $this->db->table('posts');
        $builder->select('posts.*, users.username as author_name, categories.name as category_name, categories.slug as category_slug');
        $builder->join('users', 'users.id = posts.author_id');
        $builder->join('categories', 'categories.id = posts.category_id');
        $builder->where('posts.status', 'published');
        $builder->orderBy('posts.published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    // public function getDraftPosts($authorId, $limit = null, $offset = 0)
    // {
    //     $builder = $this->db->table('posts');
    //     $builder->select('posts.*, users.username as author_name, categories.name as category_name, categories.slug as category_slug');
    //     $builder->join('users', 'users.id = posts.author_id');
    //     $builder->join('categories', 'categories.id = posts.category_id');
    //     $builder->where('posts.status', 'draft');
    //     $builder->where('posts.author_id', $authorId);
    //     $builder->orderBy('posts.created_at', 'DESC');
    //     if ($limit) {
    //         $builder->limit($limit, $offset);
    //     }
    //     return $builder->get()->getResultArray();
    // }

    public function getPostBySlug($slug, $user_id)
    {
        $builder = $this->db->table('posts');
        $builder->select('posts.*, users.username as author_name, categories.name as category_name, categories.slug as category_slug');
        $builder->join('users', 'users.id = posts.author_id');
        $builder->join('categories', 'categories.id = posts.category_id');
        $builder->where('posts.slug', $slug);

        // Allow authors to view their own drafts
        $builder->groupStart();
        $builder->where('posts.status', 'published');

        // Fix: Use proper CodeIgniter 4 syntax for OR conditions
        if ($user_id) {
            $builder->orGroupStart()
                ->where('posts.status', 'draft')
                ->where('posts.author_id', $user_id)
                ->groupEnd();
        }
        $builder->groupEnd();
        return $builder->get()->getRowArray();
    }

    public function getPostsByCategory($categoryId, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('posts');
        $builder->select('posts.*, users.username as author_name, categories.name as category_name, categories.slug as category_slug');
        $builder->join('users', 'users.id = posts.author_id');
        $builder->join('categories', 'categories.id = posts.category_id');
        $builder->where('posts.category_id', $categoryId);
        $builder->where('posts.status', 'published');
        $builder->orderBy('posts.published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    public function getPostsByAuthor($authorId,  $limit = null, $offset = 0)
    {
        $builder = $this->db->table('posts');
        $builder->select('posts.*, users.username as author_name, categories.name as category_name');
        $builder->join('users', 'users.id = posts.author_id');
        $builder->join('categories', 'categories.id = posts.category_id');
        $builder->where('posts.author_id', $authorId);
        $builder->orderBy('posts.published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    public function searchPosts($query, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('posts');
        $builder->select('posts.*, users.username as author_name, categories.name as category_name');
        $builder->join('users', 'users.id = posts.author_id');
        $builder->join('categories', 'categories.id = posts.category_id');
        $builder->groupStart()
            ->like('posts.title', $query)
            ->orLike('posts.content', $query)
            ->orLike('posts.excerpt', $query)
            ->groupEnd();
        $builder->where('posts.status', 'published');
        $builder->orderBy('posts.published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }
}
