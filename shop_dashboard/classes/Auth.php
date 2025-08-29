<?php
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private $conn;
    private $secret_key = "your-secret-key-here";
    private $issuer = "shop-dashboard";
    private $audience = "shop-users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // สร้าง JWT Token
    public function generateToken($user_data) {
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60 * 24); // 24 hours

        $payload = array(
            "iss" => $this->issuer,
            "aud" => $this->audience,
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "data" => array(
                "id" => $user_data['id'],
                "name" => $user_data['name'],
                "email" => $user_data['email'],
                "role" => $user_data['role']
            )
        );

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }

    // ตรวจสอบ JWT Token
    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            return false;
        }
    }

    // ตรวจสอบการเข้าสู่ระบบ
    public function login($email, $password) {
        $query = "SELECT id, name, email, phone, role, password 
                  FROM users WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                unset($row['password']);
                return $row;
            }
        }
        return false;
    }
}
?>