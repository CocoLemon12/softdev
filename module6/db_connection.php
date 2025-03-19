<?php
$host = "localhost"; // Update if needed
$username = "pgfbzuml_miah";
$password = "Miah@2025!";
$database = "pgfbzuml_test";// Change this to your actual database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
