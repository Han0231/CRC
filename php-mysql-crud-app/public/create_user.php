<?php
require_once '../config/database.php';

$username = 'Yuhang'; // change this
$password = 'admin123'; // change this
$role = 'admin'; // or any role you want

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, $role]);

echo "User created!";
?>