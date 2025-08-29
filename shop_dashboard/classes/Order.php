<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $user_id;
    public $total_amount;
    public $discount_amount;
    public $final_amount;
    public $status;
    public $payment_method;
    public $shipping_address;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    // อ่านคำสั่งซื้อทั้งหมด
    function read() {
        $query = "SELECT o.*, u.name as user_name 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // สร้างคำสั่งซื้อใหม่
    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET user_id=:user_id, total_amount=:total_amount, 
                      discount_amount=:discount_amount, final_amount=:final_amount,
                      status=:status, payment_method=:payment_method,
                      shipping_address=:shipping_address, notes=:notes";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":total_amount", $this->total_amount);
        $stmt->bindParam(":discount_amount", $this->discount_amount);
        $stmt->bindParam(":final_amount", $this->final_amount);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":shipping_address", $this->shipping_address);
        $stmt->bindParam(":notes", $this->notes);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // เพิ่มรายการสินค้าในคำสั่งซื้อ
    function addOrderItem($product_id, $product_name, $product_price, $product_discount, $quantity) {
        $subtotal = ($product_price * (1 - $product_discount / 100)) * $quantity;
        
        $query = "INSERT INTO order_items 
                  SET order_id=:order_id, product_id=:product_id, 
                      product_name=:product_name, product_price=:product_price,
                      product_discount=:product_discount, quantity=:quantity, 
                      subtotal=:subtotal";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":product_name", $product_name);
        $stmt->bindParam(":product_price", $product_price);
        $stmt->bindParam(":product_discount", $product_discount);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":subtotal", $subtotal);

        return $stmt->execute();
    }

    // อ่านรายการสินค้าในคำสั่งซื้อ
    function getOrderItems() {
        $query = "SELECT * FROM order_items WHERE order_id = ? ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }
}
?>