<?php
require_once 'ChargingLocation.php';
session_start();

// Ensure only admin can access
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST["description"];
    $num_stations = $_POST["num_stations"];
    $cost_per_hour = $_POST["cost_per_hour"];

    $chargingLocation = new ChargingLocation();
    if ($chargingLocation->insertLocation($description, $num_stations, $cost_per_hour)) {
        header("Location: admin_dashboard.php?message=Location added successfully");
        exit();
    } else {
        // Redirect back to the form with an error message
        header("Location: insert_location.html?error=Error inserting location");
        exit();
    }
} else {
    // If it's not a POST request, redirect to the form page
    header("Location: insert_location.html");
    exit();
}
?>