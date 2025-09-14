<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($data['seo_title']) ? $data['seo_title'] . ' - ' . SITENAME : (!empty($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME); ?></title>
    <meta name="description" content="<?php echo !empty($data['seo_description']) ? $data['seo_description'] : (!empty($data['description']) ? $data['description'] : 'Welcome to ' . SITENAME); ?>">
    <meta name="keywords" content="<?php echo !empty($data['seo_keywords']) ? $data['seo_keywords'] : (!empty($data['keywords']) ? $data['keywords'] : 'seo, webgoup, services'); ?>">
    <link rel="canonical" href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
  <div class="container">
    <a class="navbar-brand" href="<?php echo URLROOT; ?>">web<b>Goup</b></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <?php $current_uri = $_SERVER['REQUEST_URI']; ?>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_uri === '/' || $current_uri === '/index.php') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>">Home</a>
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
            <a class="nav-link <?php echo (strpos($current_uri, '/conversations') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/conversations">Messages</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_uri, '/users/dashboard') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/users/dashboard">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_uri, '/services/add') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/services/add">Add Service</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Welcome <?php echo $_SESSION['user_name']; ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo URLROOT; ?>/users/logout">Logout</a>
          </li>
        <?php else : ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_uri, '/users/register') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/users/register">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (strpos($current_uri, '/users/login') !== false) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/users/login">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
