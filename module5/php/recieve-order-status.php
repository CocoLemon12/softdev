<?php
include 'database.php';

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($order_id && $status) {
    $query = "UPDATE tbl_orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order status updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update order status."]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid order ID or status."]);
}

$conn->close();
?>
