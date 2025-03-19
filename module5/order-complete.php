<?php include 'php/database.php';

// Get the order_id from POST or GET
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : (isset($_GET['order_id']) ? $_GET['order_id'] : 0);

// Update the received date to now and change status to 'Complete'
$updateQuery = "UPDATE tbl_orders SET received_date = NOW(), order_status = 'Complete' WHERE order_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("i", $order_id);
$updateStmt->execute();
$updateStmt->close();

// Fetch order details from the database
$query = "SELECT product_id, received_date, address FROM tbl_orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($product_id, $receivedDate, $address);
$stmt->fetch();
$stmt->close();

// Fetch product details from the database
$query = "SELECT product_images, product_name, product_price FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($productImage, $productName, $productPrice);
$stmt->fetch();
$stmt->close();

$productImages = json_decode($productImage, true);
$productImage = isset($productImages[0]) ? "../module3/" . $productImages[0] : 'assets/default-product.png';
$productName = $productName ?: 'Product Name';
$productPrice = $productPrice ?: '₱0.00';
$receivedDate = $receivedDate ?: 'Received Date';
$address = $address ?: 'Address';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Complete</title>
    <link rel="icon" type="image/x-icon" href="assets/DJMLogo.png">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/order-complete.css">
</head>
<?php include 'php/nav-bar.php'; ?>
<body>
    <section>
        <div class="content">
            <div class="header">
                <a href="javascript:history.back()" class="back-button">&lt;</a>
                <h3>User Profile > My Orders > Receive Order</h3>
                <h1>Receive Order</h1>
            </div>
        </div>
    </section>
    <section class="section order-status-section">
        <div class="content order-status-content">
            <div class="order-status-text">
                <h2>Order Completed</h2>
                <p>Your order has been successfully delivered</p>
            </div>
            <img src="assets/delivered.png" alt="Order Delivered">
        </div>
    </section>
    <section class="section">
        <div class="content">
            <div class="prod-container">
                <h2>Order Details</h2>
                <div class="product">
                    <img src="<?php echo $productImage; ?>" alt="Product Image"
                        class="<?php echo empty($productImage) ? 'default-image' : ''; ?>">
                    <p><?php echo $productName; ?></p>
                </div>
                <div class="prod-info-container">
                    <div class="product-info">
                        <div class="label">
                            <p>Price</p>
                            <p>Received Date</p>
                            <p>Address</p>
                        </div>
                        <div class="input">
                            <p><?php echo $productPrice; ?></p>
                            <p><?php echo $receivedDate; ?></p>
                            <p><?php echo $address; ?></p>
                        </div>
                    </div>
                </div>
                <div class="button-actions">
                    <button class="btn" onclick="window.location.href='orders.php?status=Complete'">Continue</button>
                    <button class="btn" onclick="window.location.href='order-return-request.php?order_id=<?php echo $order_id; ?>'">Return Item</button>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
