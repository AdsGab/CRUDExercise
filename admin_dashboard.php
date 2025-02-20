<?php
include 'User.php';
include 'ChargingLocation.php';

session_start();
$user = new User();
if (!$user->isAdmin()) {
    header("Location: login.html");
    exit();
}

$chargingLocation = new ChargingLocation();

$keyword = $_GET["search"] ?? "";
$min_cost = $_GET["min_cost"] ?? "";
$max_cost = $_GET["max_cost"] ?? "";
$locations = $chargingLocation->searchLocations($keyword, $min_cost, $max_cost, "");
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

    <h3>Search Charging Locations</h3>
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label>Search by Name:</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($keyword) ?>" placeholder="Enter location name">
            </div>
            <div class="col-md-3">
                <label>Min Cost:</label>
                <input type="number" name="min_cost" class="form-control" value="<?= htmlspecialchars($min_cost) ?>" step="0.01">
            </div>
            <div class="col-md-3">
                <label>Max Cost:</label>
                <input type="number" name="max_cost" class="form-control" value="<?= htmlspecialchars($max_cost) ?>" step="0.01">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <h3>Charging Locations</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Stations Available</th>
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

    <a href="admin_checkin.php" class="btn btn-info w-100 mb-3">View All Check-ins</a>
    <a href="admin_users.php" class="btn btn-info w-100 mb-3">Manage Users</a>
    <a href="logout.php" class="btn btn-danger w-100">Logout</a>
</div>
</body>
</html>
