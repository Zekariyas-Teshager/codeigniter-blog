<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'username', 'email', 'password', 'role', 'profile_image', 'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[admin,author]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken.'
        ],
        'email' => [
            'is_unique' => 'This email is already registered.'
        ]
    ];
    
    // Hash password before insert/update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash(
                $data['data']['password'], 
                PASSWORD_DEFAULT
            );
        } else {
            // Remove password from data if empty (during update)
            unset($data['data']['password']);
        }
        return $data;
    }
    
    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }
    
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
    
    public function getAuthors()
    {
        return $this->where('role', 'author')->where('is_active', true)->findAll();
    }
}