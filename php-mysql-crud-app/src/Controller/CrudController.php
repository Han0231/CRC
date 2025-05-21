<?php
class CrudController {
    private $conn;

    public function __construct($databaseConnection) {
        $this->conn = $databaseConnection;
    }

    public function createItem($data) {
        $stmt = $this->conn->prepare("INSERT INTO items (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $data['name'], $data['description']);
        return $stmt->execute();
    }

    public function readItems() {
        $result = $this->conn->query("SELECT * FROM items");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateItem($id, $data) {
        $stmt = $this->conn->prepare("UPDATE items SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $data['name'], $data['description'], $id);
        return $stmt->execute();
    }

    public function deleteItem($id) {
        $stmt = $this->conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>