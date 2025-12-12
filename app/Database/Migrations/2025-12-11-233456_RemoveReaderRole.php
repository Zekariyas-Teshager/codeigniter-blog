<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveReaderRole extends Migration
{
    public function up()
    {
        // First, update all 'reader' users to 'author'
        $this->db->table('users')
                ->where('role', 'reader')
                ->update(['role' => 'author']);
        
        // Then modify the ENUM constraint to remove 'reader'
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'author') DEFAULT 'author'");
    }

    public function down()
    {
        // Add 'reader' back to the ENUM
        $this->db->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'author', 'reader') DEFAULT 'author'");
        
        // Optionally revert some users back to 'reader'
        // You might want to decide which users to revert
    }
}