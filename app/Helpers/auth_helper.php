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

if (!function_exists('can_create_post')) {
    function can_create_post()
    {
        // Any logged-in user can create posts
        return is_logged_in();
    }
}

if (!function_exists('can_edit_post')) {
    function can_edit_post($postAuthorId)
    {
        if (!is_logged_in()) {
            return false;
        }
        
        // Admins can edit any post, users can only edit their own
        return is_admin() || user_data('id') == $postAuthorId;
    }
}