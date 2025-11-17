<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['post_id', 'user_id', 'content', 'is_approved'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'post_id' => 'required|integer',
        'user_id' => 'required|integer',
        'content' => 'required|min_length[5]|max_length[1000]'
    ];
    
    public function getCommentsByPost($postId, $approvedOnly = true)
    {
        $builder = $this->db->table('comments');
        $builder->select('comments.*, users.username as author_name');
        $builder->join('users', 'users.id = comments.user_id');
        $builder->where('comments.post_id', $postId);
        
        if ($approvedOnly) {
            $builder->where('comments.is_approved', true);
        }
        
        $builder->orderBy('comments.created_at', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    public function getUnapprovedComments()
    {
        $builder = $this->db->table('comments');
        $builder->select('comments.*, users.username as author_name, posts.title as post_title');
        $builder->join('users', 'users.id = comments.user_id');
        $builder->join('posts', 'posts.id = comments.post_id');
        $builder->where('comments.is_approved', false);
        $builder->orderBy('comments.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}