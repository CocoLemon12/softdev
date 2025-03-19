<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost"; 
$username = "pgfbzuml_miah";
$password = "Miah@2025!";
$database = "pgfbzuml_test";

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products
$sql = "SELECT product_id, product_name, product_stock, product_sold, product_description, product_images, total_rating FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decode JSON image data (assuming it's stored as a JSON array)
        $images = json_decode($row['product_images'], true);

        // Ensure it's a valid array and get the first image
        $firstImage = (is_array($images) && count($images) > 0) ? $images[0] : 'default.jpg';

        // Debugging output (Remove after confirming images are correct)
        // var_dump($row['product_images']); 

        echo "<tr>
                <td>{$row['product_id']}</td>
                <td>
                    <img src='productimages/{$firstImage}' 
                         alt='{$row['product_name']}' 
                         width='50' 
                         onerror=\"this.onerror=null; this.src='productimages/default.jpg';\">
                </td>
                <td>{$row['product_name']}</td>
                <td>{$row['product_stock']}</td>
                <td>{$row['product_sold']}</td>
                <td class='desc'>{$row['product_description']}</td>
                <td>{$row['total_rating']}</td>
                <td><button class='edit-btn' data-id='{$row['product_id']}'>Edit</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No products found</td></tr>";
}

// Close connection
$conn->close();
?>
        