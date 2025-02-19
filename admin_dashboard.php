<?php
include 'User.php';
include 'ChargingLocation.php';

session_start();
$user = new User();
if (!$user->isAdmin()) {
    header("Location: login.php");
    exit();
}

$chargingLocation = new ChargingLocation();
$locations = $chargingLocation->getAllLocations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Welcome, Admin!</h2>
    <a href="insert_location.php" class="btn btn-success mb-3">Add New Location</a>

    <h3>Charging Locations</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Stations</th>
                <th>Cost/Hour</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $locations->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["description"] ?></td>
                    <td><?= $row["num_stations"] ?></td>
                    <td>$<?= number_format($row["cost_per_hour"], 2) ?></td>
                    <td>
                        <a href="edit_location.php?id=<?= $row["id"] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_location.php?id=<?= $row["id"] ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="logout.php" class="btn btn-danger w-100">Logout</a>
</div>
</body>
</html>
