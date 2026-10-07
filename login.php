<?php
require_once 'includes/functions.php';
if (is_logged_in()) redirect((current_user()['role'] ?? '') === 'admin' ? BASE_URL . 'admin/dashboard.php' : BASE_URL . 'index.php');
$error='';
$selected_role = post_text('user_role') === 'admin' ? 'admin' : 'student';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!valid_csrf_token()) {
    $error='Your session expired. Please refresh the page and try again.';
  } else {
    foreach (['email', 'password', 'user_role'] as $field) {
      if (isset($_POST[$field]) && !is_string($_POST[$field])) $_POST[$field] = '';
    }
  $selected_role = post_text('user_role') === 'admin' ? 'admin' : 'student';
  $email=trim($_POST['email']??''); $password=$_POST['password']??'';
  $stmt=db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1"); $stmt->execute([$email]); $u=$stmt->fetch();
  if ($u && $u['role']===$selected_role && password_verify($password,$u['password']) && $u['status']==='active') {
    session_regenerate_id(true);
    $_SESSION['user_id']=$u['id']; redirect($u['role']==='admin'?BASE_URL.'admin/dashboard.php':BASE_URL.'index.php');
  } else $error='Invalid email, password, or inactive account.';
  }
}
$page_title='Login'; include 'includes/header.php';
?>
<div class="form-card"><h1>Welcome back</h1><p class="muted">Login to manage reports and contact item owners.</p><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><?=csrf_field()?><div class="login-role-selector" role="group" aria-label="Choose account type"><button class="login-role-option<?= $selected_role === 'student' ? ' is-selected' : '' ?>" type="button" data-role="student" aria-pressed="<?= $selected_role === 'student' ? 'true' : 'false' ?>">Student</button><button class="login-role-option<?= $selected_role === 'admin' ? ' is-selected' : '' ?>" type="button" data-role="admin" aria-pressed="<?= $selected_role === 'admin' ? 'true' : 'false' ?>">Admin</button></div><input id="user-role" type="hidden" name="user_role" value="<?=e($selected_role)?>"><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" autocomplete="email" required></div><div class="field"><label for="password">Password</label><div class="password-input-wrap"><input id="password" type="password" name="password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-controls="password" aria-pressed="false">Show</button></div></div><button class="btn btn-primary" style="width:100%">Login</button></form>
<p class="muted">New student? <a style="color:#4f46e5;font-weight:700" href="<?= BASE_URL ?>register.php">Create account</a></p></div>
<script>
document.querySelectorAll('.login-role-option').forEach(function (option) {
  option.addEventListener('click', function () {
    document.getElementById('user-role').value = option.dataset.role;
    document.querySelectorAll('.login-role-option').forEach(function (item) {
      var selected = item === option;
      item.classList.toggle('is-selected', selected);
      item.setAttribute('aria-pressed', String(selected));
    });
  });
});

document.querySelectorAll('.password-toggle').forEach(function (toggle) {
  toggle.addEventListener('click', function () {
    var password = document.getElementById(toggle.getAttribute('aria-controls'));
    var isVisible = password.type === 'text';
    password.type = isVisible ? 'password' : 'text';
    toggle.textContent = isVisible ? 'Show' : 'Hide';
    toggle.setAttribute('aria-pressed', String(!isVisible));
  });
});
</script>
<?php include 'includes/footer.php'; ?>