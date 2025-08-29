<?php
require_once '../../config/cors.php';
require_once '../../middleware/admin.php';
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';

$user_data = requireAdmin();

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    $product->id = $data->id;

    if($product->delete()) {
        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "message" => "Product deleted successfully."
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to delete product."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to delete product. ID is required."
    ));
}
?>