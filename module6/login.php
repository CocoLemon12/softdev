<?php
session_start(); // Start a session

$host = "localhost"; // Update if needed
$username = "pgfbzuml_miah";
$password = "Miah@2025!";
$database = "pgfbzuml_test";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Get JSON input from request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"]) || !isset($data["password"])) {
    echo json_encode(["success" => false, "message" => "Missing email or password"]);
    exit();
}

$email = $conn->real_escape_string($data["email"]);
$password = $data["password"];

// Query the database
$sql = "SELECT * FROM tbl_admins WHERE adminEmail = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($password === $row["adminPassword"]) { 
        $_SESSION["adminName"] = $row["adminName"]; // Store admin's name in session
        echo json_encode(["success" => true, "message" => "Login successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No user found"]);
}

$conn->close();
?>
