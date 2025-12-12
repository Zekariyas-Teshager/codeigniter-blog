<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentIdToCommentsRemoveIsApproved extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comments', [
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null,
                'after' => 'user_id'
            ],
        ]);

        // Add foreign key constraint for parent_id (self-referencing)
        $this->db->query("ALTER TABLE comments ADD CONSTRAINT fk_comments_parent 
                          FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE");

        // Remove is_approved column
        $this->forge->dropColumn('comments', ['is_approved']);
            
    }

    public function down()
    {
        // Remove foreign key first
        $this->db->query("ALTER TABLE comments DROP FOREIGN KEY fk_comments_parent");
        
        $this->forge->dropColumn('comments', ['parent_id']);
        $this->forge->addColumn('comments', [
            'is_approved' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'content'
            ],
        ]);
    }
}