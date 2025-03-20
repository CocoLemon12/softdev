<?php
include 'php/database.php';

// Get the order_id from POST or GET
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : (isset($_GET['order_id']) ? $_GET['order_id'] : 0);

// Fetch order details from the database
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

// Decode JSON product images
$productImages = json_decode($order['product_images'], true);
$firstImage = isset($productImages[0]) ? "../module3/" . $productImages[0] : 'assets/default-product.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Request</title>
    <link rel="icon" type="image/x-icon" href="assets/DJMLogo.png">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/order-return-request.css">
</head>
<?php include 'php/nav-bar.php'; ?>

<body>
    <section class="section orders-section">
        <div class="content orders-content">
            <div class="orders-header">
                <a href="javascript:history.back()" class="back-button">&lt;</a>
                <h1>Return Request</h1>
            </div>
            <div class="container product-container">
                <img src="<?php echo htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Product" class="product-image">
                <div class="product-details">
                    <p><?php echo isset($order['product_name']) ? htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8') : 'Product Name'; ?></p>
                    <div class="return-warranty-container">
                        <span class="return-warranty">5 days Free Return</span>
                        <span class="return-warranty">7 days local supplier warranty</span>
                    </div>
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


            <div class="return-reason-container">
                <p class="return-reason-label">Return reason</p>
                <div class="return-reason-right" onclick="openPopup()">
                    <p id="selected-reason">Please Select</p>
                    <span class="arrow">&gt;</span>
                </div>
            </div>



            <div class="popup" id="popup-form">
                <div class="popup-header">
                    <h2>Select a Reason</h2>
                    <span class="close-popup" onclick="closePopup()">&times;</span>
                </div>
                <div class="reason-box">
                    <?php
                    $reasons = [
                        "Duplicate Order",
                        "Delivery time is too long",
                        "Damaged Item",
                        "Missing Item",
                        "Wrong Item",
                        "Defective Item",
                        "Expired product",
                        "Quality issue",
                        "Wrong Size/Color"
                    ];
                    foreach ($reasons as $index => $reason) {
                        echo '<input type="radio" id="option' . ($index + 1) . '" name="cancel-reason" value="' . htmlspecialchars($reason, ENT_QUOTES, 'UTF-8') . '">';
                        echo '<label for="option' . ($index + 1) . '">' . htmlspecialchars($reason, ENT_QUOTES, 'UTF-8') . '</label>';
                    }
                    ?>
                </div>
                <div class="popup-buttons">
                    <button onclick="submitReason()">Confirm</button>
                </div>
            </div>

            <form id="return-request-form" enctype="multipart/form-data">
                <input type="hidden" id="order-id" value="<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?>"> <!-- Ensure order ID is included -->
                <div class="upload-container">
                    <p>Upload at least a photo/video to support your return</p>
                    <div class="upload-content-container">
                        <div class="upload-content" onclick="triggerFileUpload()">
                            <img src="assets/img&vid-icon.png" alt="">
                            <p>Click to upload an image or video here</p>
                        </div>
                        <input type="file" id="fileUpload" name="fileUpload" accept="image/*, video/*" style="display: none;">
                        <p id="upload-status"></p>
                    </div>
                </div>

                <div class="container explanation-container">
                    <p>Detailed Explanation of Selected Reason</p>
                    <div class="text-input">
                        <textarea class="styled-textarea" id="detailed-reason" name="detailed-reason" placeholder="(Required)" required></textarea>
                    </div>
                </div>

                <div class="submit-return-btn">
                    <button type="submit">CONFIRM</button>
                </div>
            </form>
        </div>
    </section>

    <script src="js/order-return-request.js" defer></script>
    <script>
        function triggerFileUpload() {
            var fileUpload = document.getElementById('fileUpload');
            if (fileUpload) {
                fileUpload.click();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var fileUpload = document.getElementById('fileUpload');
            if (fileUpload) {
                fileUpload.addEventListener('change', function() {
                    var uploadStatus = document.getElementById('upload-status');
                    if (this.files.length > 0) {
                        uploadStatus.textContent = this.files[0].name + ' selected';
                    } else {
                        uploadStatus.textContent = '';
                    }
                });
            }
        });
    </script>
</body>

</html>