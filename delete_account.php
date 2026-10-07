<?php
require_once 'includes/functions.php'; require_login();
if((current_user()['role']??'')!=='student') redirect(BASE_URL.'index.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $page_title='Delete Account'; include 'includes/header.php';
  echo '<div class="form-card"><h1>Delete Account</h1><p>This action cannot be undone.</p><form method="post">' . csrf_field() . '<button class="btn btn-danger">Yes, permanently delete my account</button> <a class="btn btn-outline" href="'.BASE_URL.'profile.php">Cancel</a></form></div>';
  include 'includes/footer.php'; exit;
}
if(!valid_csrf_token()){
  flash('error','Your session expired. Please try again.');
  redirect(BASE_URL.'profile.php');
}
db()->prepare("DELETE FROM users WHERE id=? AND role='student'")->execute([$_SESSION['user_id']]);
session_unset(); session_destroy(); header('Location: '.BASE_URL.'index.php'); exit;
?>