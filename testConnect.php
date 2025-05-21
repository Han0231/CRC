<?php
$host = "127.0.0.1";  // or your specific IP
$username = "root";   // or your MySQL username
$password = "admin";       // your password (empty if no password)
$database = "example_schema";  // replace with your actual database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully to the database!";
?>
