<?php
require_once '../../config/cors.php';
require_once '../../middleware/admin.php';
require_once '../../classes/Database.php';

$user_data = requireAdmin();

$database = new Database();
$db = $database->getConnection();

// นับจำนวนสินค้า
$product_query = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
$product_stmt = $db->prepare($product_query);
$product_stmt->execute();
$total_products = $product_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// นับจำนวนผู้ใช้
$user_query = "SELECT COUNT(*) as total FROM users";
$user_stmt = $db->prepare($user_query);
$user_stmt->execute();
$total_users = $user_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// นับจำนวนคำสั่งซื้อ
$order_query = "SELECT COUNT(*) as total, SUM(final_amount) as total_sales FROM orders";
$order_stmt = $db->prepare($order_query);
$order_stmt->execute();
$order_data = $order_stmt->fetch(PDO::FETCH_ASSOC);
$total_orders = $order_data['total'];
$total_sales = $order_data['total_sales'] ?? 0;

http_response_code(200);
echo json_encode(array(
    "success" => true,
    "data" => array(
        "total_products" => intval($total_products),
        "total_users" => intval($total_users),
        "total_orders" => intval($total_orders),
        "total_sales" => floatval($total_sales)
    )
));
?>