<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToComments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('comments', [
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('comments', 'updated_at');
    }
}