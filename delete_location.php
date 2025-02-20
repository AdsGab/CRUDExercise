<?php
require_once 'ChargingLocation.php';

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$location_id = $_GET["id"] ?? "";
$chargingLocation = new ChargingLocation();

if ($location_id && $chargingLocation->deleteLocation($location_id)) {
    header("Location: admin_dashboard.php?message=Location deleted successfully.");
} else {
    header("Location: admin_dashboard.php?error=Error deleting location.");
}
exit();
?>
