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
$locations = $chargingLocation->getAvailableLocations();
$user_id = $_SESSION["user_id"];
$activeCheckins = $chargingLocation->getActiveUserCheckins($user_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Welcome, User!</h2>
        <p>Here you can find available charging locations and check-in.</p>

        <!-- Show success/error messages -->
        <?php if (isset($_GET["message"])) {
            echo "<p class='alert alert-success'>" . $_GET["message"] . "</p>";
        } ?>
        <?php if (isset($_GET["error"])) {
            echo "<p class='alert alert-danger'>" . $_GET["error"] . "</p>";
        } ?>
        <a href="user_history.php" class="btn btn-info w-100 mb-3">View Charging History</a>

        <h3>Currently Charging At</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Location</th>
                    <th>Start Time</th>
                    <th>Cost/Hour</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $activeCheckins->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row["id"] ?></td>
                        <td><?= $row["description"] ?></td>
                        <td><?= $row["start_time"] ?></td>
                        <td>$<?= number_format($row["cost_per_hour"], 2) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3>Available Charging Locations</h3>
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
                        <td><?= $row["available_stations"] ?></td> <!-- Dynamically updated -->
                        <td>$<?= number_format($row["cost_per_hour"], 2) ?></td>
                        <td>
                            <form action="checkin.php" method="post">
                                <input type="hidden" name="location_id" value="<?= $row["id"] ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Check-in</button>
                            </form>
                        </td>
                        <td>
                            <form action="checkout.php" method="post">
                                <button type="submit" class="btn btn-danger btn-sm">Check-out</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="search_locations.php" class="btn btn-info w-100 mb-3">Search Charging Locations</a>
        <a href="logout.php" class="btn btn-danger w-100">Logout</a>
    </div>
</body>

</html>