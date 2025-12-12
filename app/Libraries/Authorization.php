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

    public function canCreatePost()
    {
        // Any logged-in user can create posts
        return $this->authentication->isLoggedIn();
    }

    public function canEditPost($postAuthorId)
    {
        if (!$this->authentication->isLoggedIn()) {
            return false;
        }

        $userId = $this->authentication->getUserId();
        $userRole = $this->authentication->getUserRole();

        // Admins can edit any post, users can only edit their own posts
        return $userRole === 'admin' || $userId == $postAuthorId;
    }

    public function canDeletePost($postAuthorId)
    {
        return $this->canEditPost($postAuthorId);
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
