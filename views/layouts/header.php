<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($data['seo_title']) ? $data['seo_title'] . ' - ' . SITENAME : (!empty($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME); ?></title>
    <meta name="description" content="<?php echo !empty($data['seo_description']) ? $data['seo_description'] : (!empty($data['description']) ? $data['description'] : 'Welcome to ' . SITENAME); ?>">
    <meta name="keywords" content="<?php echo !empty($data['seo_keywords']) ? $data['seo_keywords'] : (!empty($data['keywords']) ? $data['keywords'] : 'seo, webgoup, services'); ?>">
    <link rel="canonical" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <!-- Local Fonts -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/fonts/inter.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/fonts/figtree.css">
    <!-- Bootstrap CSS -->
    <link href="<?php echo URLROOT; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/all.min.css"/>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css?v=1.1">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/custom.css?v=1.1">
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?php echo URLROOT; ?>">web<b class="text-primary">Goup</b></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="main-nav">
      <?php $current_uri = $_SERVER['REQUEST_URI']; ?>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_uri === '/' || strpos($current_uri, '/pages/index') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (strpos($current_uri, '/official') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/official">Our Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (strpos($current_uri, '/services') !== false && strpos($current_uri, '/services/add') === false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/services">Marketplace</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo (strpos($current_uri, '/pages/about') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/pages/about">About</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if(isset($_SESSION['user_id'])) : ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_uri, '/services/add') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/services/add">
              <i class="fas fa-plus-circle"></i> Add Service
            </a>
          </li>
          <li class="nav-item">
            <a id="messages-link" class="nav-link <?php echo (strpos($current_uri, '/conversations') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/conversations">Messages</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_uri, '/users/dashboard') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/users/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo $_SESSION['user_name']; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <?php if($_SESSION['user_role'] == 'admin') : ?>
                <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/admin"><i class="fas fa-user-shield"></i> Admin</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/profile/<?php echo $_SESSION['user_id']; ?>"><i class="fas fa-user-circle"></i> Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/users/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
          </li>
        <?php else : ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/login">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-primary" href="<?php echo URLROOT; ?>/users/register">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
