<?php
include 'Database.php';
session_start();

// Ensure only admin can access
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.html");
    exit();
}

// Get location details
$id = $_GET["id"];
$sql = "SELECT * FROM charging_locations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST["description"];
    $num_stations = $_POST["num_stations"];
    $cost_per_hour = $_POST["cost_per_hour"];

    $sql = "UPDATE charging_locations SET description=?, num_stations=?, cost_per_hour=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidi", $description, $num_stations, $cost_per_hour, $id);
    $stmt->execute();

    // Redirect back to dashboard
    header("Location: admin_dashboard.php?message=Location updated successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Location</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Charging Location</h2>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?= $location["id"] ?>">
        <div class="mb-3">
            <label>Description:</label>
            <input type="text" class="form-control" name="description" value="<?= $location["description"] ?>" required>
        </div>
        <div class="mb-3">
            <label>Number of Stations:</label>
            <input type="number" class="form-control" name="num_stations" value="<?= $location["num_stations"] ?>" required>
        </div>
        <div class="mb-3">
            <label>Cost per Hour:</label>
            <input type="number" class="form-control" name="cost_per_hour" value="<?= $location["cost_per_hour"] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Location</button>
    </form>
</div>
</body>
</html>
