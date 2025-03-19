<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Allowed status values
    $allowed_status = ["TO PAY", "TO SHIP", "TO RECEIVE", "COMPLETED", "CANCELLED", "RETURNED"];

    if (in_array($status, $allowed_status)) {
        $stmt = $conn->prepare("UPDATE tbl_orders SET order_Status  = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $order_id);
        error_log("Updating Order ID: $order_id, New Status: $status");
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "SUCCESS: Order status updated successfully!";
            } else {
                echo "WARNING: No rows affected. Check if order ID exists.";
            }
        } else {
            echo "ERROR: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid status!'); window.history.back();</script>";
    }
}

$conn->close();
?>
