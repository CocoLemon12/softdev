<?php
$host = "localhost";
$username = "pgfbzuml_miah";
$password = "Miah@2025!";
$database = "pgfbzuml_test";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Get Product ID & Quantity
$productID = intval($_POST["productID"]);
$quantity = intval($_POST["quantity"]);

if ($quantity <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid quantity"]);
    exit;
}

// Check Stock
$sql = "SELECT product_stock, product_sold FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Product not found"]);
    exit;
}

$row = $result->fetch_assoc();
$currentStock = $row["product_stock"];
$currentSold = $row["product_sold"];

// Ensure Enough Stock
if ($quantity > $currentStock) {
    echo json_encode(["success" => false, "message" => "Not enough stock"]);
    exit;
}

// Update Stock and Sold
$newStock = $currentStock - $quantity;
$newSold = $currentSold + $quantity;

$updateSql = "UPDATE products SET product_stock = ?, product_sold = ? WHERE product_id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("iii", $newStock, $newSold, $productID);

if ($updateStmt->execute()) {
    echo json_encode(["success" => true, "newStock" => $newStock]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update stock"]);
}

$conn->close();
?>
