<?php
session_start();
include(__DIR__ . '/db.config.php'); // Use absolute path for reliability

// Dummy user ID for testing (replace this when login is implemented)
$user_id = 1;

// Check if the connection exists
if (!isset($conn)) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Fetch cart items for user_id = 1
$sql = "
    SELECT 
        c.cart_id, 
        c.user_id, 
        c.product_id, 
        c.quantity, 
        p.product_name, 
        p.price, 
        p.image_url 
    FROM tbl_cart AS c
    INNER JOIN products AS p ON c.product_id = p.product_id
    WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed"]));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$cart_count = count($cart_items);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($cart_items);

$stmt->close();
$conn->close();
?>
