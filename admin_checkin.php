<?php
require_once 'User.php';
require_once 'ChargingLocation.php';

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}
$user_id = $_POST["user_id"];
$chargingLocation = new ChargingLocation();

$chargingLocation = new ChargingLocation();

$filter_user = $_GET["user_id"] ?? "";
$filter_status = $_GET["status"] ?? "";

$checkins = $chargingLocation->getAllCheckins($filter_user, $filter_status);
$users = (new User())->getAllUsers(); // Fetch all users for the dropdown filter

$availableLocations = $chargingLocation->getAvailableLocations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Check-in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
<h2>Check-in User</h2>
    <form action="admin_checkin_procedure.php" method="post">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        
        <div class="mb-3">
            <label>Select Charging Location:</label>
            <select class="form-control" name="location_id" required>
                <?php while ($row = $availableLocations->fetch_assoc()) { ?>
                    <option value="<?= $row["id"] ?>"><?= $row["description"] ?> (<?= $row["available_stations"] ?> stations available)</option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Confirm Check-in</button>
    </form>
<h2 class="text-center">All Check-ins</h2>
    
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label>Filter by User:</label>
                <select name="user_id" class="form-control">
                    <option value="">All Users</option>
                    <?php while ($row = $users->fetch_assoc()) { ?>
                        <option value="<?= $row['id'] ?>" <?= ($filter_user == $row['id']) ? 'selected' : '' ?>>
                            <?= $row["name"] ?> (<?= $row["email"] ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Filter by Status:</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="active" <?= ($filter_status === "active") ? "selected" : "" ?>>Active</option>
                    <option value="completed" <?= ($filter_status === "completed") ? "selected" : "" ?>>Completed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <h3>Check-in Records</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Location</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $checkins->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["user_name"] ?> (<?= $row["email"] ?>)</td>
                    <td><?= $row["description"] ?></td>
                    <td><?= $row["start_time"] ?></td>
                    <td><?= $row["end_time"] ? $row["end_time"] : "In Progress" ?></td>
                    <td>$<?= $row["total_cost"] ? number_format($row["total_cost"], 2) : "Pending" ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
   

    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
