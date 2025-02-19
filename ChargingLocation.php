<?php
require_once 'Database.php';

class ChargingLocation {
    private $conn;

    public function __construct() {
        global $conn; // Use the global connection variable
        $this->conn = $conn;
    }

    public function getAllLocations() {
        $sql = "SELECT * FROM charging_locations";
        return $this->conn->query($sql);
    }

    public function getLocationById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM charging_locations WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateLocation($id, $description, $num_stations, $cost_per_hour) {
        $stmt = $this->conn->prepare("UPDATE charging_locations SET description = ?, num_stations = ?, cost_per_hour = ? WHERE id = ?");
        $stmt->bind_param("sidi", $description, $num_stations, $cost_per_hour, $id);
        return $stmt->execute();
    }

    public function insertLocation($description, $num_stations, $cost_per_hour) {
        $stmt = $this->conn->prepare("INSERT INTO charging_locations (description, num_stations, cost_per_hour) VALUES (?, ?, ?)");
        $stmt->bind_param("sid", $description, $num_stations, $cost_per_hour);
        return $stmt->execute();
    }

    public function deleteLocation($id) {
        $stmt = $this->conn->prepare("DELETE FROM charging_locations WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function isAvailable($location_id) {
        $sql = "SELECT num_stations, (SELECT COUNT(*) FROM check_ins WHERE location_id = ? AND end_time IS NULL) AS occupied FROM charging_locations WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $location_id, $location_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return ($result["num_stations"] > $result["occupied"]);
    }
    
    public function checkInUser($user_id, $location_id) {
        if (!$this->isAvailable($location_id)) {
            return false; 
        }
    
        $sql = "INSERT INTO check_ins (user_id, location_id, start_time) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $location_id);
        return $stmt->execute();
    }
    public function getAvailableLocations() {
        $sql = "SELECT cl.*, 
                       (cl.num_stations - (SELECT COUNT(*) FROM check_ins WHERE location_id = cl.id AND end_time IS NULL)) AS available_stations
                FROM charging_locations cl
                HAVING available_stations > 0";
        return $this->conn->query($sql);
    }

    public function getActiveCheckin($user_id) {
        $sql = "SELECT * FROM check_ins WHERE user_id = ? AND end_time IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getAllCheckins($user_id = "", $status = "") {
        $sql = "SELECT ci.id, u.name AS user_name, u.email, cl.description, ci.start_time, ci.end_time, ci.total_cost 
                FROM check_ins ci
                JOIN users u ON ci.user_id = u.id
                JOIN charging_locations cl ON ci.location_id = cl.id
                WHERE 1=1";
    
        $params = [""];
    
        if (!empty($user_id)) {
            $sql .= " AND ci.user_id = ?";
            $params[0] .= "i";
            $params[] = $user_id;
        }
    
        if ($status === "active") {
            $sql .= " AND ci.end_time IS NULL";
        } elseif ($status === "completed") {
            $sql .= " AND ci.end_time IS NOT NULL";
        }
    
        $stmt = $this->conn->prepare($sql);
        if (count($params) > 1) {
            $stmt->bind_param(...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
    
    
    public function getActiveUserCheckins($user_id) {
        $sql = "SELECT ci.id, cl.description, ci.start_time, cl.cost_per_hour 
                FROM check_ins ci
                JOIN charging_locations cl ON ci.location_id = cl.id
                WHERE ci.user_id = ? AND ci.end_time IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function checkoutUser($user_id) {
        $checkin = $this->getActiveCheckin($user_id);
    
        if (!$checkin) {
            return false; // No active check-in
        }
    
        $location_id = $checkin["location_id"];
    
        // Calculate total cost
        $sql = "UPDATE check_ins 
                SET end_time = NOW(), 
                    total_cost = TIMESTAMPDIFF(HOUR, start_time, NOW()) * 
                    (SELECT cost_per_hour FROM charging_locations WHERE id = ?)
                WHERE user_id = ? AND location_id = ? AND end_time IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $location_id, $user_id, $location_id);
        $stmt->execute();
        $stmt->close();
    
        return true;
    }
    public function getUserHistory($user_id) {
        $sql = "SELECT ci.id, cl.description, ci.start_time, ci.end_time, ci.total_cost 
                FROM check_ins ci
                JOIN charging_locations cl ON ci.location_id = cl.id
                WHERE ci.user_id = ?
                ORDER BY ci.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getUserHistoryFiltered($user_id, $start_date, $end_date, $location_id) {
        $sql = "SELECT ci.id, cl.description, ci.start_time, ci.end_time, ci.total_cost 
                FROM check_ins ci
                JOIN charging_locations cl ON ci.location_id = cl.id
                WHERE ci.user_id = ?";
    
        $params = ["i", $user_id];
    
        if (!empty($start_date)) {
            $sql .= " AND ci.start_time >= ?";
            $params[0] .= "s";
            $params[] = $start_date;
        }
    
        if (!empty($end_date)) {
            $sql .= " AND ci.end_time <= ?";
            $params[0] .= "s";
            $params[] = $end_date;
        }
    
        if (!empty($location_id)) {
            $sql .= " AND ci.location_id = ?";
            $params[0] .= "i";
            $params[] = $location_id;
        }
    
        $sql .= " ORDER BY ci.start_time DESC";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function getUserLocations($user_id) {
        $sql = "SELECT DISTINCT cl.id, cl.description 
                FROM check_ins ci
                JOIN charging_locations cl ON ci.location_id = cl.id
                WHERE ci.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function searchLocations($keyword, $min_cost, $max_cost, $availability) {
        $sql = "SELECT cl.*, 
                       (cl.num_stations - (SELECT COUNT(*) FROM check_ins WHERE location_id = cl.id AND end_time IS NULL)) AS available_stations
                FROM charging_locations cl
                WHERE cl.description LIKE ?";
    
        $params = ["s", "%".$keyword."%"];
    
        if (!empty($min_cost)) {
            $sql .= " AND cl.cost_per_hour >= ?";
            $params[0] .= "d";
            $params[] = $min_cost;
        }
    
        if (!empty($max_cost)) {
            $sql .= " AND cl.cost_per_hour <= ?";
            $params[0] .= "d";
            $params[] = $max_cost;
        }
    
        if ($availability === "available") {
            $sql .= " HAVING available_stations > 0";
        } elseif ($availability === "full") {
            $sql .= " HAVING available_stations = 0";
        }
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    
}
?>
