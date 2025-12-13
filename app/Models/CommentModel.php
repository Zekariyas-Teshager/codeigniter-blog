<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $allowedFields = ['post_id', 'user_id', 'content', 'parent_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'post_id' => 'required|integer',
        'user_id' => 'required|integer',
        'content' => 'required|min_length[5]|max_length[1000]'
    ];

    public function getCommentsByPost($postId)
    {
        $builder = $this->db->table('comments');
        $builder->select('comments.*, users.username as author_name');
        $builder->join('users', 'users.id = comments.user_id');
        $builder->where('comments.post_id', $postId);
        $builder->where('comments.parent_id IS NULL'); // Only main comments
        $builder->orderBy('comments.created_at', 'ASC');

        $comments = $builder->get()->getResultArray();

        // Get reply counts for each comment
        if (!empty($comments)) {
            $commentIds = array_column($comments, 'id');

            $replyBuilder = $this->db->table('comments');
            $replyBuilder->select('parent_id, COUNT(*) as reply_count');
            $replyBuilder->whereIn('parent_id', $commentIds);
            $replyBuilder->groupBy('parent_id');

            $replyCounts = $replyBuilder->get()->getResultArray();

            // Create lookup array
            $replyCountMap = [];
            foreach ($replyCounts as $count) {
                $replyCountMap[$count['parent_id']] = (int)$count['reply_count'];
            }

            // Add reply count to each comment
            foreach ($comments as &$comment) {
                $comment['reply_count'] = $replyCountMap[$comment['id']] ?? 0;
            }
        }

        return $comments;
    }

    public function getReplies($commentId, $page = 1, $limit = 5)
    {
        $offset = ($page - 1) * $limit;

        $builder = $this->db->table('comments as c');
        $builder->select('c.*, u.username as author_name');
        $builder->join('users as u', 'u.id = c.user_id', 'left');
        $builder->where('c.parent_id', $commentId);
        $builder->orderBy('c.created_at', 'ASC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResultArray();
    }

    // Get total reply count for pagination
    public function getTotalReplies($commentId)
    {
        return $this->db->table('comments')
            ->where('parent_id', $commentId)
            ->countAllResults();
    }
}
