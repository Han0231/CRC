<?php
$host = "localhost";  // or your specific IP
$username = "root";   // or your MySQL username
$password = "admin";       // your password (empty if no password)
$database = "test";  // replace with your actual database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully to the database!";
?>
