<?php
require_once '../../config/cors.php';
require_once '../../classes/Database.php';
require_once '../../classes/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // ดึงสินค้าทั้งหมด
        $stmt = $product->read();
        $products = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $product_item = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "description" => $row['description'],
                "price" => floatval($row['price']),
                "discount" => floatval($row['discount']),
                "category_id" => $row['category_id'],
                "category_name" => $row['category_name'],
                "image_url" => $row['image_url'],
                "stock_quantity" => intval($row['stock_quantity']),
                "status" => $row['status'],
                "created_at" => $row['created_at']
            );
            array_push($products, $product_item);
        }

        http_response_code(200);
        echo json_encode(array(
            "success" => true,
            "data" => $products
        ));
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}
?>