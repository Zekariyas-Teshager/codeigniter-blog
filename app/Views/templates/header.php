<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $site_title ?? 'CodeIgniter Blog' ?> - <?= $title ?? 'Welcome' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .post-card {
            transition: transform 0.2s;
        }

        .post-card:hover {
            transform: translateY(-2px);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url() ?>">
                <i class="fas fa-blog"></i> CodeIgniter Blog
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url() ?>">Home</a>
                    </li>
                    <!-- <?php if (is_logged_in() && can_create_post()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('posts/my-posts') ?>">
                                My Post
                            </a>
                        </li>
                    <?php endif; ?> -->
                    <?php if ($categories): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Categories
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= site_url('category/' . $category['slug']) ?>">
                                            <?= esc($category['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (is_logged_in() && can_create_post()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('posts/create') ?>">
                                <i class="fas fa-plus"></i> Write Post
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="<?= site_url('admin') ?>">
                                <i class="fas fa-cog"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <?php if (user_data('profile_image')): ?>
                                    <img src="<?= base_url('uploads/' . user_data('profile_image')) ?>" class="user-avatar me-1">
                                <?php else: ?>
                                    <i class="fas fa-user me-1"></i>
                                <?php endif; ?>
                                <?= esc(user_data('username')) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= site_url('profile') ?>"><i class="fas fa-user"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="<?= is_admin() ? site_url('admin') : site_url('dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= site_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex justify-content-end container mt-3">
        <form class="d-flex me-2" action="<?= base_url('search') ?>" method="get">
            <div class="input-group">
                <input type="search" class="form-control border-primary" name="q" placeholder="Search posts..."
                    value="<?= service('request')->getGet('q') ?? ''  ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <main class="container my-4">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('info') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>