<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Posts about technology and programming',
                'created_at' => new Time('now'),
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Travel experiences and tips',
                'created_at' => new Time('now'),
            ],
            [
                'name' => 'Food',
                'slug' => 'food',
                'description' => 'Recipes and food reviews',
                'created_at' => new Time('now'),
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Daily life and personal development',
                'created_at' => new Time('now'),
            ]
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}