<?php

namespace App\Libraries;

class Authorization
{
    protected $authentication;
    
    public function __construct()
    {
        $this->authentication = new Authentication();
    }
    
    public function hasPermission($requiredRole)
    {
        if (!$this->authentication->isLoggedIn()) {
            return false;
        }
        
        $userRole = $this->authentication->getUserRole();
        
        // Define role hierarchy
        $roleHierarchy = [
            'reader' => 1,
            'author' => 2,
            'admin' => 3
        ];
        
        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    public function canEditPost($postAuthorId)
    {
        if (!$this->authentication->isLoggedIn()) {
            return false;
        }
        
        $userId = $this->authentication->getUserId();
        $userRole = $this->authentication->getUserRole();
        
        // Admin and authors can edit their own posts
        return ($userRole === 'admin' && $userId == $postAuthorId) || ($userRole === 'author' && $userId == $postAuthorId);
    }
    
    public function canDeletePost($postAuthorId)
    {
        if (!$this->authentication->isLoggedIn()) {
            return false;
        }
        
        $userId = $this->authentication->getUserId();
        $userRole = $this->authentication->getUserRole();
        
        // Admin can delete any post and authors can only delete their own posts
        return ($userRole === 'admin') || ($userRole === 'author' && $userId == $postAuthorId);
    }
    
    public function canCreatePost()
    {
        return $this->hasPermission('author');
    }
    
    public function checkPermission($requiredRole, $redirectTo = 'login')
    {
        if (!$this->hasPermission($requiredRole)) {
            return redirect()->to($redirectTo)->with('error', 'You do not have permission to access this page.');
        }
    }
    
    public function checkPostPermission($postAuthorId, $redirectTo = 'posts')
    {
        if (!$this->canEditPost($postAuthorId)) {
            return redirect()->to($redirectTo)->with('error', 'You do not have permission to edit this post.');
        }
    }
}