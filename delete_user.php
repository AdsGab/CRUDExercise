<?php
require_once 'User.php';

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$user = new User();
$user_id = $_GET["id"] ?? "";

if ($user_id && $user->deleteUser($user_id)) {
    header("Location: admin_users.php?message=User deleted successfully.");
} else {
    header("Location: admin_users.php?error=Error deleting user.");
}
exit();
?>
