<?php
include 'php/database.php';

// Get the order_id from POST or GET
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : (isset($_GET['order_id']) ? $_GET['order_id'] : 0);

// Fetch order details from tbl_orders
$query = "SELECT tbl_orders.*, products.product_name, products.product_images 
          FROM tbl_orders 
          JOIN products ON tbl_orders.product_id = products.product_id 
          WHERE tbl_orders.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Decode product images
$productImages = isset($order['product_images']) ? json_decode($order['product_images'], true) : [];
$firstImage = isset($productImages[0]) ? "../module3/" . $productImages[0] : 'assets/default-product.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Order</title>
    <link rel="icon" type="image/x-icon" href="assets/DJMLogo.png">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/order-cancellation-request.css">
</head>
<?php include 'php/nav-bar.php'; ?>

<body>
    <section class="section">
        <div class="content">
            <div class="orders-header">
                <a href="javascript:history.back()" class="back-button">&lt;</a>
                <h1>Cancel Request Form</h1>
            </div>

            <div class="container product-container">
                <img src="<?php echo htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Product" class="product-image">
                <div class="product-details">
                    <p><?php echo isset($order['product_name']) ? htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8') : 'Missing product name data'; ?></p>
                    <div class="price">
                        <!-- Price container -->
                        <div class="price-amount">
                            <span class="peso">₱</span>
                            <span class="price-value">
                                <?php echo isset($order['total_price']) ? htmlspecialchars($order['total_price'], ENT_QUOTES, 'UTF-8') : '0.00'; ?>
                            </span>
                        </div>

                        <!-- Quantity container -->
                        <div class="quantity">
                            <span class="qty-label">Qty:</span>
                            <span class="qty-value">
                                <?php echo isset($order['order_quantity']) ? htmlspecialchars($order['order_quantity'], ENT_QUOTES, 'UTF-8') : '0'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Reason -->
            <div class="container cancel-reason-container">
                <p>Cancellation Reason</p>
                <div class="cancel-reason-button" onclick="openPopup()">
                    <p id="selected-reason">Please Select</p>
                    <p><span>&gt;</span></p>
                </div>
            </div>

            <!-- Popup Modal -->
            <div class="popup" id="popup-form">
                <div class="popup-header">
                    <h2>Select a Reason</h2>
                    <span class="close-popup" onclick="closePopup()">&times;</span>
                </div>
                <div class="reason-box">
                    <input type="radio" id="option1" name="cancel-reason" value="Duplicate Order">
                    <label for="option1">Duplicate Order</label>

                    <input type="radio" id="option2" name="cancel-reason" value="Delivery time is too long">
                    <label for="option2">Delivery time is too long</label>

                    <input type="radio" id="option3" name="cancel-reason" value="Change payment method">
                    <label for="option3">Change payment method</label>

                    <input type="radio" id="option4" name="cancel-reason" value="Found better price elsewhere">
                    <label for="option4">Found better price elsewhere</label>

                    <input type="radio" id="option5" name="cancel-reason" value="Seller request to cancel">
                    <label for="option5">Seller request to cancel</label>

                    <input type="radio" id="option6" name="cancel-reason" value="I change my mind">
                    <label for="option6">I change my mind</label>

                    <input type="radio" id="option7" name="cancel-reason" value="I want to change my shipping information">
                    <label for="option7">I want to change my shipping information</label>

                    <input type="radio" id="option8" name="cancel-reason" value="Change/Combine Order">
                    <label for="option8">Change/Combine Order</label>

                    <input type="radio" id="option9" name="cancel-reason" value="Shipping cost too high">
                    <label for="option9">Shipping cost too high</label>

                    <input type="radio" id="option10" name="cancel-reason" value="Others">
                    <label for="option10">Others (please give an explanation below)</label>
                </div>
                <div class="popup-buttons">
                    <button type="button" onclick="submitReason()">Select</button>
                </div>
            </div>

            <!-- Detailed Explanation -->
            <div class="container explanation-container">
                <p>Detailed Explanation of Selected Reason</p>
                <div class="text-input">
                    <textarea class="styled-textarea" id="detailed-reason" placeholder="(Required)" required></textarea>
                </div>
            </div>

            <form id="cancellation-form">
                <input type="hidden" id="order-id" value="<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="submit-cancel-btn">
                    <button type="submit">SUBMIT CANCELLATION</button>
                </div>
            </form>
        </div>
    </section>
    <script src="js/order-cancellation-request.js" defer></script>
</body>

</html>