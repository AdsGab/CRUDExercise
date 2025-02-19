<?php
require_once 'User.php';
require_once 'ChargingLocation.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$location_id = $_POST["location_id"];

$chargingLocation = new ChargingLocation();

// Attempt to check-in
if ($chargingLocation->checkInUser($user_id, $location_id)) {
    header("Location: user_dashboard.php?message=Check-in successful!");
} else {
    header("Location: user_dashboard.php?error=No available stations at this location.");
}
exit();
?>
