<?php
require_once __DIR__ . '/functions.php';
$user = current_user();
if ($user && $user['status'] !== 'active') $user = null;
$page_title = $page_title ?? 'Smart Campus Lost & Found';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> | Smart Campus</title>
<link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
<header class="topbar<?= $user ? ($user['role'] === 'admin' ? ' topbar-admin' : ' topbar-student') : '' ?>">
  <a class="brand" href="<?= BASE_URL ?>index.php"><span class="brand-mark">SC</span><span>Smart Campus<br><small>Lost & Found</small></span></a>
  <nav>
    <a href="<?= BASE_URL ?>index.php">Home</a>
    <a href="<?= BASE_URL ?>lost_items.php">Lost Items</a>
    <a href="<?= BASE_URL ?>found_items.php">Found Items</a>
    <?php if ($user && $user['role']==='admin'): ?>
      <a href="<?= BASE_URL ?>admin/dashboard.php">Admin Dashboard</a>
    <?php endif; ?>
  </nav>
  <div class="nav-actions">
    <?php if ($user): ?>
      <?php if ($user['role'] === 'student'): ?>
        <details class="user-menu">
          <summary class="btn btn-student-menu user-menu-toggle"><span class="logout-user-name"><?= e($user['name']) ?></span></summary>
          <div class="user-menu-dropdown">
            <a href="<?= BASE_URL ?>profile.php">My Profile</a>
            <a href="<?= BASE_URL ?>my_posts.php">My Posts</a>
          </div>
        </details>
        <form class="logout-form" method="post" action="<?= BASE_URL ?>logout.php"><?= csrf_field() ?><button class="btn btn-logout" type="submit">Logout</button></form>
      <?php else: ?>
        <form class="logout-form" method="post" action="<?= BASE_URL ?>logout.php"><?= csrf_field() ?><button class="btn btn-logout" type="submit"><span class="logout-user-name"><?= e($user['name']) ?></span><span class="logout-divider" aria-hidden="true"></span><span>Logout</span></button></form>
      <?php endif; ?>
    <?php else: ?>
      <a class="btn btn-outline" href="<?= BASE_URL ?>login.php">Login</a>
      <a class="btn btn-primary" href="<?= BASE_URL ?>register.php">Register</a>
    <?php endif; ?>
  </div>
</header>
<main class="container">
<?php show_flashes(); ?>