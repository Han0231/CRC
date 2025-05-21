<?php
function getTables($conn) {
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    return $tables;
}

function createTable($conn, $tableName) {
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    $sql = "CREATE TABLE `$tableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL
    )";
    $conn->exec($sql);
}

function deleteTable($conn, $tableName) {
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    $conn->exec("DROP TABLE `$tableName`");
}

function getRows($conn, $table) {
    $stmt = $conn->query("SELECT * FROM `$table`");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addRow($conn, $table, $name, $description) {
    $stmt = $conn->prepare("INSERT INTO `$table` (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $description]);
}

function deleteRow($conn, $table, $id) {
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
}
?>