<?php
include 'db_connection.php'; // Ensure this file connects to your database

header('Content-Type: application/json');

$response = [];

// Total Sales (sum of total_Price)
$sqlTotalSales = "SELECT SUM(total_Price) AS total_sales FROM tbl_orders";
$result = $conn->query($sqlTotalSales);
$response['total_sales'] = $result->fetch_assoc()['total_sales'] ?? 0;

// Total Orders (count of orders)
$sqlTotalOrders = "SELECT COUNT(*) AS total_orders FROM tbl_orders";
$result = $conn->query($sqlTotalOrders);
$response['total_orders'] = $result->fetch_assoc()['total_orders'] ?? 0;

// Total Sold (count of COMPLETED orders)
$sqlTotalSold = "SELECT COUNT(*) AS total_sold FROM tbl_orders WHERE order_Status = 'COMPLETED'";
$result = $conn->query($sqlTotalSold);
$response['total_sold'] = $result->fetch_assoc()['total_sold'] ?? 0;

// Total Users (count of users)
$sqlTotalUsers = "SELECT COUNT(*) AS total_users FROM tbl_users";
$result = $conn->query($sqlTotalUsers);
$response['total_users'] = $result->fetch_assoc()['total_users'] ?? 0;

echo json_encode($response);
$conn->close();
?>
