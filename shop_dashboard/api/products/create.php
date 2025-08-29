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

if(!empty($data->name) && !empty($data->price) && !empty($data->category_id)) {
    $product->name = $data->name;
    $product->description = $data->description ?? '';
    $product->price = $data->price;
    $product->discount = $data->discount ?? 0;
    $product->category_id = $data->category_id;
    $product->image_url = $data->image_url ?? '';
    $product->stock_quantity = $data->stock_quantity ?? 0;

    if($product->create()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Product created successfully.",
            "id" => $product->id
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "success" => false,
            "message" => "Unable to create product."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to create product. Data is incomplete."
    ));
}
?>