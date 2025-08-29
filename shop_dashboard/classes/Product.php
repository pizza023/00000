<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $price;
    public $discount;
    public $category_id;
    public $image_url;
    public $stock_quantity;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // อ่านสินค้าทั้งหมด
    function read() {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active'
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // อ่านสินค้าตาม ID
    function readOne() {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = ? AND p.status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->discount = $row['discount'];
            $this->category_id = $row['category_id'];
            $this->image_url = $row['image_url'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    // สร้างสินค้าใหม่
    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET name=:name, description=:description, price=:price, 
                      discount=:discount, category_id=:category_id, 
                      image_url=:image_url, stock_quantity=:stock_quantity";

        $stmt = $this->conn->prepare($query);

        // ทำความสะอาดข้อมูล
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->discount = htmlspecialchars(strip_tags($this->discount));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));

        // ผูกค่า
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":discount", $this->discount);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // อัปเดตสินค้า
    function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name=:name, description=:description, price=:price,
                      discount=:discount, category_id=:category_id,
                      image_url=:image_url, stock_quantity=:stock_quantity
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->discount = htmlspecialchars(strip_tags($this->discount));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":discount", $this->discount);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // ลบสินค้า (soft delete)
    function delete() {
        $query = "UPDATE " . $this->table_name . " SET status='inactive' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // ค้นหาสินค้า
    function search($keywords) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->execute();
        return $stmt;
    }
}
?>