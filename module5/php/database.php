<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "pgfbzuml_test";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}
