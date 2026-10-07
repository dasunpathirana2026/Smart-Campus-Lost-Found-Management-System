<?php
require_once 'includes/functions.php';
require_login();

$user = current_user();
if (($user['role'] ?? '') !== 'student') redirect(BASE_URL . 'admin/dashboard.php');

$error = '';
$editing_field = '';
$form_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editing_field = isset($_POST['field']) && is_string($_POST['field']) ? trim($_POST['field']) : '';
    $form_value = post_text('value');

    if (!valid_csrf_token()) {
        $error = 'Your session expired. Please refresh the page and try again.';
    } elseif (!in_array($editing_field, ['name', 'email', 'phone'], true)) {
        $error = 'Select a valid profile detail to update.';
    } elseif ($editing_field === 'name' && ($form_value === '' || strlen($form_value) > 100)) {
        $error = 'Enter a name of up to 100 characters.';
    } elseif ($editing_field === 'email' && (!filter_var($form_value, FILTER_VALIDATE_EMAIL) || strlen($form_value) > 150)) {
        $error = 'Enter a valid email address of up to 150 characters.';
    } elseif ($editing_field === 'phone' && ($form_value === '' || strlen($form_value) > 30)) {
        $error = 'Enter a phone number of up to 30 characters.';
    } else {
        try {
            db()->beginTransaction();
            $stmt = db()->prepare("UPDATE users SET {$editing_field} = ? WHERE id = ? AND role = 'student'");
            $stmt->execute([$form_value, $user['id']]);

            if ($editing_field === 'phone') {
                $stmt = db()->prepare('UPDATE posts SET phone = ? WHERE user_id = ?');
                $stmt->execute([$form_value, $user['id']]);
            }

            db()->commit();
            flash('success', 'Your ' . $editing_field . ' has been updated.');
            redirect(BASE_URL . 'profile.php');
        } catch (PDOException $exception) {
            if (db()->inTransaction()) db()->rollBack();
            if ($exception->getCode() === '23000') {
                $error = 'That email address is already in use.';
            } else {
                error_log($exception->getMessage());
                $error = 'Your profile could not be updated. Please try again.';
            }
        }
    }
}

$page_title = 'My Profile';
include 'includes/header.php';
?>
<div class="section-head profile-page-heading">
  <div>
    <h2>My Profile</h2>
    <p class="muted">Manage your account details and reports.</p>
  </div>
</div>

<div class="profile-layout">
  <section class="info-card profile-section" aria-labelledby="student-details-heading">
    <h3 id="student-details-heading">Student Details</h3>
    <p class="muted">Update the contact details associated with your account.</p>
    <?php if ($error && $editing_field === ''): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php foreach (['name' => 'Full Name', 'email' => 'Email', 'phone' => 'Phone Number'] as $field => $label): ?>
      <div class="profile-detail-row">
        <div class="profile-detail-value">
          <span class="muted"><?= e($label) ?></span>
          <strong><?= e($user[$field]) ?></strong>
        </div>
        <details class="profile-edit"<?= $editing_field === $field ? ' open' : '' ?>>
          <summary aria-label="Edit <?= e(strtolower($label)) ?>" title="Edit <?= e(strtolower($label)) ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
          </summary>
          <form class="profile-edit-form" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="field" value="<?= e($field) ?>">
            <div class="field">
              <label for="profile-<?= e($field) ?>"><?= e($label) ?></label>
              <input id="profile-<?= e($field) ?>" name="value" <?= $field === 'email' ? 'type="email" autocomplete="email"' : ($field === 'phone' ? 'type="tel" autocomplete="tel"' : 'autocomplete="name"') ?> maxlength="<?= $field === 'name' ? '100' : ($field === 'email' ? '150' : '30') ?>" value="<?= e($editing_field === $field ? $form_value : $user[$field]) ?>" required>
            </div>
            <?php if ($error && $editing_field === $field): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
            <button class="btn btn-primary" type="submit">Save</button>
          </form>
        </details>
      </div>
    <?php endforeach; ?>
    <p class="muted">Member since <?= e(date('d M Y', strtotime($user['created_at']))) ?></p>
  </section>

  <section class="info-card profile-section profile-posts" aria-labelledby="my-posts-heading">
    <div>
      <h3 id="my-posts-heading">My Posts</h3>
      <p class="muted">View and manage your lost and found reports.</p>
    </div>
    <a class="btn btn-primary" href="<?= BASE_URL ?>my_posts.php">Go to My Posts</a>
  </section>
</div>
<section class="danger-zone profile-danger-zone" aria-labelledby="delete-account-heading">
  <div>
    <h3 id="delete-account-heading">Delete Account</h3>
    <p class="muted">This permanently removes your account and all of your posts. This action cannot be undone.</p>
  </div>
  <a class="btn btn-danger" href="<?= BASE_URL ?>delete_account.php">Delete My Account</a>
</section>
<?php include 'includes/footer.php'; ?>
