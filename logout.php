<?php
require_once 'includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Logout requires a POST request.');
}
if (!valid_csrf_token()) {
    http_response_code(403);
    exit('Logout request could not be verified.');
}
session_unset();
session_destroy();
header('Location: ' . BASE_URL . 'index.php');
exit;
?>