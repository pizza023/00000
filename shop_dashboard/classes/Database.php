<?php
require_once '../config/database.php';

class DatabaseConnection {
    private $database;
    
    public function __construct() {
        $this->database = new Database();
    }
    
    public function getConnection() {
        return $this->database->getConnection();
    }
}
?>