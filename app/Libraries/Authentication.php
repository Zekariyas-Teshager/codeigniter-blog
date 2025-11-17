<?php

namespace App\Libraries;

use App\Models\UserModel;
use Config\Auth as AuthConfig;

class Authentication
{
    protected $userModel;
    protected $authConfig;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authConfig = new AuthConfig();
        $this->session = \Config\Services::session();
    }
    
    public function login($email, $password)
    {
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user || !$user['is_active']) {
            return false;
        }
        
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return false;
        }
        
        // Set user session
        $this->setUserSession($user);
        
        return true;
    }
    
    public function loginWithUsername($username, $password)
    {
        $user = $this->userModel->getUserByUsername($username);
        
        if (!$user || !$user['is_active']) {
            return false;
        }
        
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return false;
        }
        
        $this->setUserSession($user);
        
        return true;
    }
    
    public function register($userData)
    {
        try {
            // Set default role if not provided
            if (!isset($userData['role'])) {
                $userData['role'] = $this->authConfig->defaultRole;
            }
            
            return $this->userModel->insert($userData);
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return false;
        }
    }
    
    public function logout()
    {
        $this->session->remove($this->authConfig->sessionKey);
    }
    
    public function isLoggedIn()
    {
        return $this->session->has($this->authConfig->sessionKey);
    }
    
    public function getUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->session->get($this->authConfig->sessionKey);
    }
    
    public function getUserId()
    {
        $user = $this->getUser();
        return $user ? $user['id'] : null;
    }
    
    public function getUserRole()
    {
        $user = $this->getUser();
        return $user ? $user['role'] : null;
    }
    
    public function isAdmin()
    {
        return $this->getUserRole() === 'admin';
    }
    
    public function isAuthor()
    {
        return $this->getUserRole() === 'author';
    }
    
    public function isReader()
    {
        return $this->getUserRole() === 'reader';
    }
    
    private function setUserSession($user)
    {
        $sessionData = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'profile_image' => $user['profile_image'],
            'is_active' => $user['is_active'],
            'logged_in' => true
        ];
        
        $this->session->set($this->authConfig->sessionKey, $sessionData);
    }
}