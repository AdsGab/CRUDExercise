<?php
require_once 'Database.php';

class User {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function register($name, $email, $phone, $password, $type) {
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement
        $sql = "INSERT INTO users (name, email, phone, password, type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $type);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            return false;
        }
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_type"] = $user["user_type"];
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    public function isAdmin() {
        return isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin";
    }

    public function getAllUsers() {
        $sql = "SELECT id, name, email, phone, type FROM users ORDER BY type, name";
        return $this->conn->query($sql);
    }

    public function searchUsers($search = "", $type = "user") {
        $sql = "SELECT id, name, email, phone, type FROM users WHERE (name LIKE ? OR email LIKE ?) AND type = ?";
        $params = ["sss", "%".$search."%", "%".$search."%", $type];
    
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(...$params);
            $stmt->execute();
            return $stmt->get_result();
        } else {
            return false;
        }
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, name, email, phone, type FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function updateUser($id, $name, $email, $phone, $type) {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, type = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $type, $id);
        return $stmt->execute();
    }
    public function deleteUser($id) {
        // Delete all check-in records related to this user
        $sql1 = "DELETE FROM check_ins WHERE user_id = ?";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        $stmt1->close();
    
        // Delete the user
        $sql2 = "DELETE FROM users WHERE id = ?";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $result = $stmt2->execute();
        $stmt2->close();
    
        return $result;
    }
    
    
}
?>
