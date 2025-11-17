<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        $auth = service('authentication');
        return $auth->isLoggedIn();
    }
}

if (!function_exists('user_data')) {
    function user_data($key = null)
    {
        $auth = service('authentication');
        $user = $auth->getUser();
        
        if ($key === null) {
            return $user;
        }
        
        return $user[$key] ?? null;
    }
}

if (!function_exists('has_role')) {
    function has_role($requiredRole)
    {
        $authz = service('authorization');
        return $authz->hasPermission($requiredRole);
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return has_role('admin');
    }
}

if (!function_exists('is_author')) {
    function is_author()
    {
        return has_role('author');
    }
}

if (!function_exists('can_edit_post')) {
    function can_edit_post($postAuthorId)
    {
        $authz = service('authorization');
        return $authz->canEditPost($postAuthorId);
    }
}

if (!function_exists('can_delete_post')) {
    function can_delete_post($postAuthorId)
    {
        $authz = service('authorization');
        return $authz->canDeletePost($postAuthorId);
    }
}

if (!function_exists('can_create_post')) {
    function can_create_post()
    {
        $authz = service('authorization');
        return $authz->canCreatePost();
    }
}