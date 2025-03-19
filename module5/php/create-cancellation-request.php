<?php
include 'database.php';

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$user_id = 1; // Assuming user_id is 1 for now
$date_submitted = date('Y-m-d H:i:s');
$cancel_status = 'Pending';

if ($order_id && $reason && $description) {
    $conn->begin_transaction();
    try {
        // Insert cancellation request
        $query = "INSERT INTO tbl_cancellations (user_id, order_id, return_reason, description, date_submitted, cancel_status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissss", $user_id, $order_id, $reason, $description, $date_submitted, $cancel_status);
        $stmt->execute();
        $stmt->close();

        // Update order status in tbl_orders
        $updateQuery = "UPDATE tbl_orders SET order_status = 'Cancelled' WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $order_id);
        $updateStmt->execute();
        $updateStmt->close();

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Cancellation request created and order status updated to 'Cancelled'."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Failed to create cancellation request and update order status."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data provided."]);
}

$conn->close();
?>
