<?php
require 'db_connection.php'; // Ensure database connection

header('Content-Type: application/json');

// Check if the connection is successful
if (!$conn) {
    echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
    exit();
}

// Corrected SQL query using order_Arrival for dates
$sql = "SELECT DATE_FORMAT(order_Arrival, '%m') AS month, SUM(total_Price) AS total_revenue
        FROM tbl_orders
        WHERE YEAR(order_Arrival) = YEAR(CURRENT_DATE)
        GROUP BY DATE_FORMAT(order_Arrival, '%m')
        ORDER BY month";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit();
}

$revenueData = [];
while ($row = $result->fetch_assoc()) {
    $revenueData[$row['month']] = $row['total_revenue'];
}

echo json_encode($revenueData);
$conn->close();
?>
