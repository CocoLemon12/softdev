<?php include 'php/database.php';

// Get the order_id from POST
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

if ($order_id > 0) {
    // Update the received_date to now and change status to 'Complete'
    $updateQuery = "UPDATE tbl_orders SET received_date = NOW(), order_status = 'Complete' WHERE order_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $order_id);
    $updateStmt->execute();
    $updateStmt->close();
}

// Close DB connection
$conn->close();


header("Location: order-details.php?order_id={$order_id}");
exit;
