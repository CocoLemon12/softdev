<?php
include 'database.php';

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$media_content = isset($_POST['media_content']) ? base64_decode($_POST['media_content']) : null;
$user_id = 1; // Assuming user_id is 1 for now
$date_submitted = date('Y-m-d H:i:s');
$return_status = 'Pending';

if ($order_id && $reason && $description && $media_content !== null) {
    $conn->begin_transaction();
    try {
        // Insert return request
        $query = "INSERT INTO tbl_returns (user_id, order_id, return_reason, media_proof, description, date_submitted, return_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisssss", $user_id, $order_id, $reason, $media_content, $description, $date_submitted, $return_status);
        $stmt->execute();
        $return_id = $stmt->insert_id; // Get the return_id
        $stmt->close();

        // Update order status to 'Return' in tbl_orders
        $updateQuery = "UPDATE tbl_orders SET order_status = 'Return' WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $order_id);
        $updateStmt->execute();
        $updateStmt->close();

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Return request created and order status updated to 'Return'.", "return_id" => $return_id]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Failed to create return request and update order status."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid data provided."]);
}

$conn->close();
?>
