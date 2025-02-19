<?php
require_once 'Database.php';
session_start();

global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    try {
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_type"] = $user["type"];

                // Redirect based on user type
                if ($user["type"] == "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "No account found with this email.";
        }
    } catch (mysqli_sql_exception $e) {
        die("Error: " . $e->getMessage());
    }

    $stmt->close();
}
?>
