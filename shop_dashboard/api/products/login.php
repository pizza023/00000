<?php
require_once '../../config/cors.php';
require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password)) {
    $user_data = $auth->login($data->email, $data->password);
    
    if($user_data) {
        $token = $auth->generateToken($user_data);
        
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Login successful.",
            "token" => $token,
            "user" => $user_data
        ));
    } else {
        http_response_code(401);
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid email or password."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Email and password are required."
    ));
}
?>