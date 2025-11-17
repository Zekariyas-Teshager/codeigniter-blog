<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'email' => 'admin@blog.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => new Time('now'),
                'updated_at' => new Time('now'),
            ],
            [
                'username' => 'author1',
                'email' => 'author1@blog.com',
                'password' => password_hash('author123', PASSWORD_DEFAULT),
                'role' => 'author',
                'is_active' => true,
                'created_at' => new Time('now'),
                'updated_at' => new Time('now'),
            ],
            [
                'username' => 'reader1',
                'email' => 'reader1@blog.com',
                'password' => password_hash('reader123', PASSWORD_DEFAULT),
                'role' => 'reader',
                'is_active' => true,
                'created_at' => new Time('now'),
                'updated_at' => new Time('now'),
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}