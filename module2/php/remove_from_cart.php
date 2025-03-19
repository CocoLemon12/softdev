<?php
session_start();
header('Content-Type: application/json'); // Ensure JSON response
include(__DIR__ . '/db.config.php'); // Ensure correct path

// Dummy user ID for testing (replace when login is implemented)
$user_id = 1;

// Check if the connection exists
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Get raw POST data and decode JSON
$input = json_decode(file_get_contents("php://input"), true);

// Validate cart ID
if (!isset($input['cart_id'])) {
    echo json_encode(["success" => false, "message" => "Cart ID is missing"]);
    exit;
}

if (!is_numeric($input['cart_id'])) {
    echo json_encode(["success" => false, "message" => "Cart ID is not a number"]);
    exit;
}


$cart_id = intval($input['cart_id']);

// Prepare the delete query
$query = "DELETE FROM tbl_cart WHERE user_id = ? AND cart_id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("ii", $user_id, $cart_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item removed from cart"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to remove item"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Query preparation failed"]);
}

$conn->close();
?>
