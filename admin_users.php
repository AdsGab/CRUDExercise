<?php
require_once 'User.php';

session_start();
$user = new User();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$search = $_GET["search"] ?? "";
$type = "user";  //This is to ensure all the selected user from the databaes are of the normal user type not Admin

$users = $user->searchUsers($search, $type);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Manage Users</h2>
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <label>Search by Name or Email:</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Enter name or email">
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <h3>All Registered Users (Excluding Admins)</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["email"] ?></td>
                    <td><?= $row["phone"] ?></td>
                    <td><?= ucfirst($row["type"]) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $row["id"] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_user.php?id=<?= $row["id"] ?>" class="btn btn-danger btn-sm">Delete</a>
                        <form action="admin_checkin.php" method="post" class="d-inline">
                            <input type="hidden" name="user_id" value="<?= $row["id"] ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Check-in</button>
                        </form>

                        <form action="admin_checkout.php" method="post" class="d-inline">
                            <input type="hidden" name="user_id" value="<?= $row["id"] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Check-out</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
