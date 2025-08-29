<?php
require_once '../classes/Auth.php';
require_once '../config/database.php';

function requireAuth() {
    $headers = getallheaders();
    $token = null;

    if(isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    }

    if(!$token) {
        http_response_code(401);
        echo json_encode(array("message" => "Access denied. Token required."));
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    $auth = new Auth($db);
    
    $user_data = $auth->validateToken($token);
    
    if(!$user_data) {
        http_response_code(401);
        echo json_encode(array("message" => "Access denied. Invalid token."));
        exit();
    }

    return $user_data;
}
?>