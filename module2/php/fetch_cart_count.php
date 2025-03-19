<?php
session_start();
include(__DIR__ . '/db.config.php'); // Ensure correct path

// Dummy user ID for testing (replace when login is implemented)
$user_id = 1;

// Check if the connection exists
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Get cart count for user_id = 1
$sql = "SELECT COUNT(cart_id) AS cart_count FROM tbl_cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed"]));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$cart_count = $row['cart_count'] ?? 0; // Default to 0 if no items

// Return JSON response with just the cart count
header('Content-Type: application/json');
echo json_encode(["cart_count" => $cart_count]);

$stmt->close();
$conn->close();
?>
