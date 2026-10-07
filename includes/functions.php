<?php
require_once __DIR__ . '/../config.php';

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function is_logged_in(): bool {
    $user = current_user();
    return $user !== null && $user['status'] === 'active';
}

function current_user(): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    static $loaded = false;
    static $user = null;
    if (!$loaded) {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
        $loaded = true;
    }
    return $user;
}

function require_login(): void {
    $user = current_user();
    if (!$user || $user['status'] !== 'active') {
        unset($_SESSION['user_id']);
        flash('error', 'Please log in with an active account to continue.');
        redirect(BASE_URL . 'login.php');
    }
}

function require_admin(): void {
    require_login();
    if ((current_user()['role'] ?? '') !== 'admin') redirect(BASE_URL . 'index.php');
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function show_flashes(): void {
    foreach ($_SESSION['flash'] ?? [] as $f) {
        echo '<div class="alert ' . e($f['type']) . '">' . e($f['message']) . '</div>';
    }
    unset($_SESSION['flash']);
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function valid_csrf_token(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && is_string($_POST['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function post_text(string $key): string {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function is_valid_item_date(string $value): bool {
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = DateTimeImmutable::getLastErrors();
    return $date !== false
        && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
        && $date->format('Y-m-d') === $value
        && $date <= new DateTimeImmutable('today');
}

function upload_image(array $file): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) throw new RuntimeException('Image must be under 5MB.');

    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!class_exists('finfo') || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Image upload could not be verified. Please try again.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) throw new RuntimeException('Only JPG, PNG or WEBP images are allowed.');

    $name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $dir = __DIR__ . '/../uploads/';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Image storage is unavailable. Please contact the administrator.');
    }
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) throw new RuntimeException('Could not save image.');
    return 'uploads/' . $name;
}
?>