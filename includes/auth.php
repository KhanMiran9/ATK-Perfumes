<?php
require_once 'db.php';

class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($email, $password) {
        $query = "SELECT id, name, email, password_hash, role_id FROM " . $this->table_name . " 
                  WHERE email = :email AND status = 'active' LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role_id'];
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        session_destroy();
        session_unset();
    }

    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) return false;
        
        $query = "SELECT p.name FROM permissions p
                  JOIN role_permissions rp ON p.id = rp.permission_id
                  WHERE rp.role_id = :role_id AND p.name = :permission";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role_id', $_SESSION['user_role']);
        $stmt->bindParam(':permission', $permission);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function register($name, $email, $password, $phone = null, $address = null) {
        // Check if email already exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return false; // Email already exists
        }
        
        // Insert new user
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, email, password_hash, role_id, phone, address, status, created_at) 
                  VALUES (:name, :email, :password_hash, 4, :phone, :address, 'active', NOW())";
        
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        
        if ($stmt->execute()) {
            return $this->login($email, $password);
        }
        return false;
    }
}
?>