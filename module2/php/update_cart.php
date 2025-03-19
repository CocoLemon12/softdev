<?php
session_start();
include(__DIR__ . '/db.config.php'); // Ensure correct path to database config

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['product_id'], $data['quantity'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$product_id = (int) $data['product_id'];
$new_quantity = (int) $data['quantity'];

// Dummy user ID for testing (replace this when login is implemented)
$user_id = 1;

// Ensure database connection
if (!isset($conn)) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Update cart quantity
$sql = "UPDATE tbl_cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Cart updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update cart"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "SQL error"]);
}

$conn->close();
?>
