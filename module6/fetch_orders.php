<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// ✅ Make sure connection is valid
if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

// ✅ SQL Query (Modify table names as needed)
$sql = "SELECT 
            orders.order_id , 
            orders.userID, 
            CONCAT(users.first_name, ' ', users.last_name) AS full_name, 
            orders.date_Ordered, 
            products.product_name, 
            orders.order_Qty, 
            orders.order_Status, 
            orders.address, 
            orders.total_Price
        FROM tbl_orders AS orders
        JOIN tbl_users AS users ON orders.userID = users.userID
        JOIN products AS products ON orders.productID = products.product_id";

$result = $conn->query($sql);

$orders = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} else {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

// ✅ Always return valid JSON (even if empty)
header('Content-Type: application/json');
echo json_encode($orders);

$conn->close();
?>
