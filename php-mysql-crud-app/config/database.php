<?php
$host = "shortline.proxy.rlwy.net:39121";
$username = "root";
$password = "luTpskzdzuiWyCpmkTLGBSatrGmrzBIa";
$database = "railway";

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>