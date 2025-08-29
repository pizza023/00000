<?php
class Cart {
    private $conn;
    private $table_name = "cart";

    public $id;
    public $user_id;
    public $product_id;
    public $quantity;

    public function __construct($db) {
        $this->conn = $db;
    }

    // อ่านตะกร้าสินค้าของผู้ใช้
    function readByUser() {
        $query = "SELECT c.*, p.name, p.price, p.discount, p.image_url 
                  FROM " . $this->table_name . " c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ? AND p.status = 'active'
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        return $stmt;
    }

    // เพิ่มสินค้าในตะกร้า
    function add() {
        // ตรวจสอบว่ามีสินค้านี้ในตะกร้าแล้วหรือไม่
        $query = "SELECT id, quantity FROM " . $this->table_name . " 
                  WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            // อัปเดตจำนวน
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $row['quantity'] + $this->quantity;
            
            $update_query = "UPDATE " . $this->table_name . " 
                            SET quantity = ? WHERE id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(1, $new_quantity);
            $update_stmt->bindParam(2, $row['id']);
            return $update_stmt->execute();
        } else {
            // เพิ่มใหม่
            $insert_query = "INSERT INTO " . $this->table_name . "
                            SET user_id=:user_id, product_id=:product_id, quantity=:quantity";
            $insert_stmt = $this->conn->prepare($insert_query);
            $insert_stmt->bindParam(":user_id", $this->user_id);
            $insert_stmt->bindParam(":product_id", $this->product_id);
            $insert_stmt->bindParam(":quantity", $this->quantity);
            return $insert_stmt->execute();
        }
    }

    // อัปเดตจำนวนสินค้าในตะกร้า
    function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET quantity = ? 
                  WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->quantity);
        $stmt->bindParam(2, $this->user_id);
        $stmt->bindParam(3, $this->product_id);
        return $stmt->execute();
    }

    // ลบสินค้าจากตะกร้า
    function remove() {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        return $stmt->execute();
    }

    // ล้างตะกร้าทั้งหมด
    function clear() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        return $stmt->execute();
    }
}
?>