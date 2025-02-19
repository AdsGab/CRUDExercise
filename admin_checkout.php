<?php
require_once 'ChargingLocation.php';

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$user_id = $_POST["user_id"];
$chargingLocation = new ChargingLocation();

if ($chargingLocation->checkoutUser($user_id)) {
    header("Location: admin_users.php?message=User successfully checked out.");
} else {
    header("Location: admin_users.php?error=User has no active check-in.");
}
exit();
?>
