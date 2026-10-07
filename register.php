<?php
require_once 'includes/functions.php';
if (is_logged_in()) redirect(BASE_URL.'index.php');
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!valid_csrf_token()) {
    $error='Your session expired. Please refresh the page and try again.';
  } else {
    foreach (['name', 'email', 'phone', 'password'] as $field) {
      if (isset($_POST[$field]) && !is_string($_POST[$field])) $_POST[$field] = '';
    }
  $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $phone=trim($_POST['phone']??''); $password=$_POST['password']??'';
  if ($name==='' || strlen($name)>100 || $phone==='' || strlen($phone)>30 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<6) $error='Please enter valid details. Password must be at least 6 characters.';
  else {
    try { $stmt=db()->prepare("INSERT INTO users(name,email,phone,password,role,status) VALUES(?,?,?,?, 'student','active')"); $stmt->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT)]); flash('success','Account created. You can now log in.'); redirect(BASE_URL.'login.php'); }
    catch(PDOException $e){ $error=$e->getCode()==='23000'?'Email is already registered.':'Registration failed.'; }
  }
  }
}
$page_title='Register'; include 'includes/header.php';
?>
<div class="form-card"><h1>Create Student Account</h1><p class="muted">Join your campus lost & found community.</p><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><?=csrf_field()?><div class="field"><label for="name">Full Name</label><input id="name" name="name" maxlength="100" autocomplete="name" required></div><div class="field"><label for="email">Email</label><input id="email" type="email" name="email" autocomplete="email" required></div><div class="field"><label for="phone">Phone Number</label><input id="phone" name="phone" maxlength="30" autocomplete="tel" required></div><div class="field"><label for="password">Password</label><div class="password-input-wrap"><input id="password" type="password" name="password" minlength="6" autocomplete="new-password" required><button class="password-toggle" type="button" aria-controls="password" aria-pressed="false">Show</button></div></div><button class="btn btn-primary" style="width:100%">Create Account</button></form></div>
<script>
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