<?php
require_once 'ChargingLocation.php';

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$user_id = $_POST["user_id"];
$location_id = $_POST["location_id"];

$chargingLocation = new ChargingLocation();

if ($chargingLocation->checkInUser($user_id, $location_id)) {
    header("Location: admin_users.php?message=User successfully checked in.");
} else {
    header("Location: admin_users.php?error=Failed to check-in user.");
}
exit();
?>
