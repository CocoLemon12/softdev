<?php
ob_clean(); // Clear any previous output
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// ✅ Ensure connection is valid
if (!$conn) {
    die(json_encode(["error" => "Database connection failed"]));
}

// ✅ SQL Query: Get total sales per category per month
$sql = "SELECT 
            p.product_category, 
            DATE_FORMAT(o.order_Arrival, '%Y-%m') AS sale_month, 
            SUM(o.order_Qty) AS total_sold
        FROM tbl_orders AS o
        JOIN products AS p ON o.productID = p.product_id
        GROUP BY p.product_category, sale_month
        ORDER BY sale_month ASC";

$result = $conn->query($sql);

$salesData = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['product_category'];
        $month = $row['sale_month'];
        $salesData[$category][$month] = (int) $row['total_sold'];
    }
} else {
    die(json_encode(["error" => "Query failed: " . $conn->error]));
}

// ✅ Ensure all categories have values for each month
$allMonths = [];
foreach ($salesData as $category => $months) {
    $allMonths = array_unique(array_merge($allMonths, array_keys($months)));
}
sort($allMonths);

foreach ($salesData as $category => &$months) {
    foreach ($allMonths as $month) {
        if (!isset($months[$month])) {
            $months[$month] = 0;
        }
    }
    ksort($months); // Sort months in ascending order
}

// ✅ Return JSON data
header('Content-Type: application/json');
echo json_encode(["months" => $allMonths, "sales" => $salesData]);

$conn->close();
?>
