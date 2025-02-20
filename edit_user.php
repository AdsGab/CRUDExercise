<?php
require_once 'User.php';

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$user = new User();
$user_id = $_GET["id"] ?? "";

if (!$user_id) {
    header("Location: admin_users.php?error=Invalid user ID.");
    exit();
}

$userdata = $user->getUserById($user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $type = $_POST["type"];

    if ($user->updateUser($user_id, $name, $email, $phone, $type)) {
        header("Location: admin_users.php?message=User updated successfully.");
    } else {
        echo "Error updating user.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit User</h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $userdata["id"] ?>">
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" class="form-control" name="name" value="<?= $userdata["name"] ?>" required>
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" class="form-control" name="email" value="<?= $userdata["email"] ?>" required>
        </div>
        <div class="mb-3">
            <label>Phone:</label>
            <input type="text" class="form-control" name="phone" value="<?= $userdata["phone"] ?>" required>
        </div>
        <div class="mb-3">
            <label>User Type:</label>
            <select class="form-control" name="type" required>
                <option value="user" <?= ($userdata["type"] === "user") ? "selected" : "" ?>>User</option>
                <option value="admin" <?= ($userdata["type"] === "admin") ? "selected" : "" ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update User</button>
    </form>
</div>
</body>
</html>
