<?php 
require_once 'User.php';
require_once 'ChargingLocation.php';

session_start();
$user = new User();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$chargingLocation = new ChargingLocation();

$keyword = $_GET["keyword"] ?? "";
$min_cost = $_GET["min_cost"] ?? "";
$max_cost = $_GET["max_cost"] ?? "";
$availability = $_GET["availability"] ?? "";

$locations = $chargingLocation->searchLocations($keyword, $min_cost, $max_cost, $availability);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Charging Locations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Search Charging Locations</h2>
    
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label>Keyword:</label>
                <input type="text" name="keyword" class="form-control" value="<?= $keyword ?>" placeholder="Enter location name">
            </div>
            <div class="col-md-2">
                <label>Min Cost:</label>
                <input type="number" name="min_cost" class="form-control" value="<?= $min_cost ?>" step="0.01">
            </div>
            <div class="col-md-2">
                <label>Max Cost:</label>
                <input type="number" name="max_cost" class="form-control" value="<?= $max_cost ?>" step="0.01">
            </div>
            <div class="col-md-2">
                <label>Availability:</label>
                <select name="availability" class="form-control">
                    <option value="">Any</option>
                    <option value="available" <?= ($availability === "available") ? "selected" : "" ?>>Available</option>
                    <option value="full" <?= ($availability === "full") ? "selected" : "" ?>>Full</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <h3>Search Results</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Stations Available</th>
                <th>Cost/Hour</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $locations->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["description"] ?></td>
                    <td><?= $row["available_stations"] ?></td>
                    <td>$<?= number_format($row["cost_per_hour"], 2) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
