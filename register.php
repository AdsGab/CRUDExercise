<?php
require_once 'User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $type = $_POST["type"];

    $user = new User();
    if ($user->register($name, $email, $phone, $password, $type)) {
        header("Location: success.html");
        exit();
    } else {
        echo "Error registering user.";
    }
}
?>
