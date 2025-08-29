<?php
require_once 'auth.php';

function requireAdmin() {
    $user_data = requireAuth();
    
    if($user_data['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(array("message" => "Access denied. Admin role required."));
        exit();
    }

    return $user_data;
}
?>