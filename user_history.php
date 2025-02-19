<?php 
require_once 'User.php';
require_once 'ChargingLocation.php';

session_start();
$user = new User();

// Ensure only regular users can access this page
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] === "admin") {
    header("Location: login.php");
    exit();
}

$chargingLocation = new ChargingLocation();
$user_id = $_SESSION["user_id"];

$start_date = $_GET["start_date"] ?? "";
$end_date = $_GET["end_date"] ?? "";
$location_id = $_GET["location_id"] ?? "";

$history = $chargingLocation->getUserHistoryFiltered($user_id, $start_date, $end_date, $location_id);
$locations = $chargingLocation->getUserLocations($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Charging History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Your Charging History</h2>
    <p>Filter by date or location to view specific sessions.</p>

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label>Start Date:</label>
                <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
            </div>
            <div class="col-md-4">
                <label>End Date:</label>
                <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
            </div>
            <div class="col-md-4">
                <label>Location:</label>
                <select name="location_id" class="form-control">
                    <option value="">All Locations</option>
                    <?php while ($row = $locations->fetch_assoc()) { ?>
                        <option value="<?= $row['id'] ?>" <?= ($location_id == $row['id']) ? 'selected' : '' ?>>
                            <?= $row["description"] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filter</button>
    </form>

    <h3>Past Charging Sessions</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Location</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $history->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["description"] ?></td>
                    <td><?= $row["start_time"] ?></td>
                    <td><?= $row["end_time"] ? $row["end_time"] : "In Progress" ?></td>
                    <td>$<?= $row["total_cost"] ? number_format($row["total_cost"], 2) : "Pending" ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
