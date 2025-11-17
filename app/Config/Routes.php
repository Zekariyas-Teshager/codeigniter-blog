<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home page
$routes->get('/', 'Home::index');

// Authentication routes
$routes->get('login', 'Auth::login', ['filter' => 'guest']);
$routes->post('login', 'Auth::login', ['filter' => 'guest']);
$routes->get('register', 'Auth::register', ['filter' => 'guest']);
$routes->post('register', 'Auth::register', ['filter' => 'guest']);
$routes->get('logout', 'Auth::logout');
$routes->get('forgot-password', 'Auth::forgotPassword', ['filter' => 'guest']);
$routes->post('forgot-password', 'Auth::forgotPassword', ['filter' => 'guest']);

// Dashboard routes
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('profile', 'Dashboard::profile', ['filter' => 'auth']);
$routes->post('profile', 'Dashboard::profile', ['filter' => 'auth']);

// Blog routes
$routes->get('posts', 'Blog::index');
$routes->get('post/(:segment)', 'Blog::view/$1');
$routes->get('category/(:segment)', 'Blog::category/$1');
$routes->get('search', 'Blog::search');

// Protected blog routes
$routes->group('posts', ['filter' => 'auth'], function($routes) {
    $routes->get('create', 'Blog::create');
    $routes->post('create', 'Blog::create');
    $routes->get('edit/(:num)', 'Blog::edit/$1');
    $routes->post('edit/(:num)', 'Blog::edit/$1');
    $routes->get('delete/(:num)', 'Blog::delete/$1');
    $routes->get('my-posts', 'Blog::myPosts');
});

// Comments routes
$routes->post('comments/add', 'Comments::add');
$routes->get('comments/approve/(:num)', 'Comments::approve/$1', ['filter' => 'role:admin']);
$routes->get('comments/delete/(:num)', 'Comments::delete/$1');
$routes->get('comments/manage', 'Comments::manage', ['filter' => 'role:admin']);

// Admin routes
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('users', 'Admin::users');
    $routes->get('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->get('categories', 'Admin::categories');
    $routes->get('categories/create', 'Admin::createCategory');
    $routes->post('categories/create', 'Admin::createCategory');
    $routes->get('categories/delete/(:num)', 'Admin::deleteCategory/$1');
});