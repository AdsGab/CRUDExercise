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
$chargingLocation = new ChargingLocation();

// Attempt to check-out
if ($chargingLocation->checkoutUser($user_id)) {
    header("Location: user_dashboard.php?message=Check-out successful! Total cost calculated.");
} else {
    header("Location: user_dashboard.php?error=No active check-in found.");
}
exit();
?>
