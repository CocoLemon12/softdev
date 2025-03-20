<?php
include 'php/database.php';

// Get the order_id from POST or GET
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : (isset($_GET['order_id']) ? $_GET['order_id'] : 0);

// Fetch order details from the database
$query = "SELECT tbl_orders.*, products.product_name, products.product_images, products.product_id, products.product_price, tbl_users.phone, tbl_orders.total_price 
          FROM tbl_orders 
          JOIN products ON tbl_orders.product_id = products.product_id 
          JOIN tbl_users ON tbl_orders.user_id = tbl_users.userID 
          WHERE tbl_orders.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Fetch cancellation details if order is cancelled
if ($order && strtolower($order['order_status']) === 'cancelled') {
    $cancelQuery = "SELECT cancel_status FROM tbl_cancellations WHERE order_id = ?";
    $cancelStmt = $conn->prepare($cancelQuery);
    $cancelStmt->bind_param("i", $order_id);
    $cancelStmt->execute();
    $cancelResult = $cancelStmt->get_result();
    $cancellation = $cancelResult->fetch_assoc();
    $cancelStmt->close();
}

// Fetch return details if order is returned
if ($order && strtolower($order['order_status']) === 'return') {
    $returnQuery = "SELECT return_status, pick_up_date FROM tbl_returns WHERE order_id = ?";
    $returnStmt = $conn->prepare($returnQuery);
    $returnStmt->bind_param("i", $order_id);
    $returnStmt->execute();
    $returnResult = $returnStmt->get_result();
    $return = $returnResult->fetch_assoc();
    $returnStmt->close();
}

$conn->close();

// Decode JSON image data
$productImages = $order && isset($order['product_images'])
    ? json_decode($order['product_images'], true)
    : [];
$firstImage = isset($productImages[0]) ? "../module3/" . $productImages[0] : "../module5/assets/default-product.png";

/**
 * Helper function to display user-friendly order details based on status,
 * with two buttons if it's a completed order:
 *   1) Return/Refund (enabled only if status is 'complete'/'completed')
 *   2) Buy Again
 */
function displayOrderDetails($order, $cancellation = null, $return = null)
{
    $productName  = $order['product_name'] ?? 'Missing product name data';
    $productName  = htmlspecialchars($productName, ENT_QUOTES, 'UTF-8');
    $orderDate    = isset($order['date_ordered'])
        ? date('d M Y', strtotime($order['date_ordered']))
        : 'Missing order date data';
    $arrivalDate  = isset($order['order_arrival'])
        ? date('d M Y', strtotime($order['order_arrival']))
        : 'Missing arrival date data';
    $receivedDate = isset($order['received_date'])
        ? date('d M Y', strtotime($order['received_date']))
        : 'Missing received date data';

    $status       = strtolower($order['order_status']);
    $isComplete   = ($status === 'complete' || $status === 'completed');

    // Return/Refund button
    $refundButton = $isComplete
        ? "<form action='order-return-request.php' method='POST' style='display:inline;'>
             <input type='hidden' name='order_id' value='{$order['order_id']}'>
             <button type='submit' class='order-action-button return-refund-button'>Return/Refund</button>
           </form>"
        : "<button class='order-action-button return-refund-button' disabled>Return/Refund</button>";

    // Buy Again button
    $buyAgainButton = "
        <form action='../module3/productdescrption.php' method='POST' style='display:inline;'>
            <input type='hidden' name='product_id' value='{$order['product_id']}'>
            <button type='submit' class='order-action-button buy-again-button'>Buy Again</button>
        </form>
    ";

    switch ($status) {
        case 'processing':
            return "
                <p>{$productName}</p>
                <p style='font-size:18px; color: purple; font-weight: 500; font-style: italic;'>
                    We are currently processing your order.
                </p>
                <div class='order-info order-date'>
                    Ordered Date - <strong>{$orderDate}</strong>
                </div>
                <div class='order-info order-arrival'>
                    Estimated Arrival - <strong>{$arrivalDate}</strong>
                </div>
                <div class='order-info order-status'>
                    Order Status - <strong>Processing</strong>
                </div>
                <div class='order-actions' style='display:flex; justify-content:flex-end; gap:10px;'>
                    {$refundButton}
                    {$buyAgainButton}
                </div>
            ";

        case 'shipped':
            return "
                <p>{$productName}</p>
                <p style='font-size:18px; color: purple; font-weight: 500; font-style: italic;'>
                    Your order is on its way and will be delivered soon.
                </p>
                <div class='order-info order-date'>Ordered Date - <strong>{$orderDate}</strong></div>
                <div class='order-info order-arrival'>Estimated Arrival - <strong>{$arrivalDate}</strong></div>
                <div class='order-info order-status'>Order Status - <strong>In Transit</strong></div>
                <div class='order-actions' style='display:flex; justify-content:flex-end; gap:10px;'>
                    {$refundButton}
                    {$buyAgainButton}
                </div>
            ";

        case 'complete':
        case 'completed':
            return "
                <p>{$productName}</p>
                <p style='font-size:18px; color: purple; font-weight: 500; font-style: italic;'>
                    Your order has been delivered successfully.
                </p>
                <div class='order-info order-arrival'>Delivered On - <strong>{$receivedDate}</strong></div>
                <div class='order-info order-status'>Order Status - <strong>Delivered</strong></div>
                <div class='order-info order-date'>Ordered Date - <strong>{$orderDate}</strong></div>
                <div class='order-actions' style='display:flex; justify-content:flex-end; gap:10px;'>
                    {$refundButton}
                    {$buyAgainButton}
                </div>
            ";

        case 'cancelled':
            // Show the cancellation status if available
            $cancelStatus = $cancellation['cancel_status'] ?? 'Missing cancel status';
            $cancelStatus = htmlspecialchars($cancelStatus, ENT_QUOTES, 'UTF-8');
            return "
                <p>{$productName}</p>
                <p style='font-size:18px; color: purple; font-weight: 500; font-style: italic;'>
                    Your order was cancelled. If you need further assistance, contact support.
                </p>
                <div class='order-info order-status'>Order Status - <strong>Cancelled</strong></div>
                <div class='order-info order-date'>Ordered Date - <strong>{$orderDate}</strong></div>
                <div class='order-info order-date'>Cancel Status - <strong>{$cancelStatus}</strong></div>
                <div class='order-actions' style='display:flex; justify-content:flex-end; gap:10px;'>
                    {$buyAgainButton}
                </div>
            ";

        case 'return':
            // Show return info
            $returnStatus = $return['return_status'] ?? 'Missing return status';
            $returnStatus = htmlspecialchars($returnStatus, ENT_QUOTES, 'UTF-8');
            $pickUpDate   = isset($return['pick_up_date'])
                ? date('d M Y', strtotime($return['pick_up_date']))
                : 'Missing pick up date';
            return "
                <p>{$productName}</p>
                <div class='order-info order-status'>Order Status - <strong>Return</strong></div>
                <div class='order-info order-date'>Ordered Date - <strong>{$orderDate}</strong></div>
                <div class='order-info order-date'>Return Status - <strong>{$returnStatus}</strong></div>
                <div class='order-info order-date'>Pick Up Date - <strong>{$pickUpDate}</strong></div>
                <p>Your return request is in progress. If you need further assistance, contact support.</p>
                <div class='order-actions' style='display:flex; justify-content:flex-end; gap:10px;'>
                    {$buyAgainButton}
                </div>
            ";

        default:
            return "
                <p>{$productName}</p>
                <div class='order-info order-status'>Order Status - Unknown</div>
                <div class='order-actions' style='display:flex; justify-content:flex-end; gap:10px;'>
                    {$refundButton}
                    {$buyAgainButton}
                </div>
            ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="icon" type="image/x-icon" href="assets/DJMLogo.png">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/order-details.css">
</head>
<?php include 'php/nav-bar.php'; ?>

<body>
    <section class="section">
        <div class="content">

            <?php
            // Decide heading text based on status
            $headingText = 'Order Details';
            // If order exists and status is cancelled, change heading
            if ($order && strtolower($order['order_status']) === 'cancelled') {
                $headingText = 'Cancellation Details';
            }
            ?>

            <!-- Top Heading -->
            <div class="orders-header">
                <a href="javascript:history.back()" class="back-button">&lt;</a>
                <h1><?php echo $headingText; ?></h1>
            </div>

            <!-- If Cancelled, show the purple banner "Cancellation in Process" -->
            <?php if ($order && strtolower($order['order_status']) === 'cancelled'): ?>
                <section class="section order-status-section">
                    <div class="content order-status-content">
                        <div class="order-status-text">
                            <h2>Cancellation in Process</h2>
                            <p>Your order has been successfully cancelled. We are now confirming the cancellation status with the seller.</p>
                        </div>
                        <!-- Example image on the right -->
                        <img src="assets/delivered.png" alt="Order Delivered">
                    </div>
                </section>
            <?php endif; ?>

            <!-- If Cancelled, build the new "Cancellation" card structure -->
            <?php if ($order && strtolower($order['order_status']) === 'cancelled'): ?>

                <!-- Main Card Container -->
                <div class="cancellation-card">
                    <h2>Cancellation</h2> <!-- Title of the card -->

                    <!-- Card Body: Image on left, product info on right -->
                    <div class="cancellation-body">
                        <img src="<?php echo htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'); ?>"
                            alt="Product Image" class="product-image">

                        <div class="product-info">
                            <!-- Product Name with an extended descriptive title -->
                            <p class="product-name">
                                <?php echo htmlspecialchars($order['product_name'] ?? 'Missing product name', ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <!-- Return/Warranty badges -->
                            <div class="return-warranty-container">
                                <span class="return-warranty">5 days Free Return</span>
                                <span class="return-warranty">7 days local supplier warranty</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Details: 
                     - Quantity
                     - Date (pick whichever date you consider the 'cancellation date')
                     - Cancelled by (e.g., "Buyer")
                     - Reason (the cancel_status from your DB)
                -->
                    <div class="cancellation-details">
                        <h3>Cancellation Details</h3>
                        <div class="details-grid">
                            <!-- Quantity -->
                            <div class="detail-label">Quantity</div>
                            <div class="detail-value" style="font-weight: bold;"><?php echo (int)($order['order_quantity'] ?? 0); ?></div>

                            <!-- Cancellation Date -->
                            <div class="detail-label">Cancellation Date</div>
                            <div class="detail-value" style="font-weight: bold;"><?php echo date('d M Y'); ?></div>

                            <!-- Cancelled By (Hardcode "Buyer" or derive from logic) -->
                            <div class="detail-label">Cancelled By</div>
                            <div class="detail-value" style="font-weight: bold;">Buyer</div>

                            <!-- Reason from $cancellation['cancel_status'] -->
                            <div class="detail-label">Reason</div>
                            <div class="detail-value" style="font-weight: bold;">
                                <?php
                                $cancelStatus = $cancellation['cancel_status'] ?? 'No reason found';
                                echo htmlspecialchars($cancelStatus, ENT_QUOTES, 'UTF-8');
                                ?>
                            </div>
                        </div>

                    </div>

                </div> <!-- End .cancellation-card -->

                <!-- Bottom Info (Order ID, Shipping Address, Payment Method, etc.) -->
                <div class="order-details">
                    <div class="order-info-grid">
                        <div class="order-data">
                            <strong>Order ID:</strong>
                            <?php echo htmlspecialchars($order['order_id'] ?? 'Missing order ID', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Shipping Address:</strong>
                            <?php echo htmlspecialchars($order['address'] ?? 'Missing address', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Payment Method:</strong>
                            <?php echo htmlspecialchars($order['payment_method'] ?? 'Missing payment method', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Contact Number:</strong>
                            <?php echo htmlspecialchars($order['phone'] ?? 'Missing contact number', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Product Name:</strong>
                            <?php echo htmlspecialchars($order['product_name'] ?? 'Missing product name', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>

                    <!-- Price/Qty/Shipping/Total in a smaller grid on the right or below -->
                    <div class="order-calculations">
                        <div class="order-data"><strong>Price:</strong></div>
                        <span class="calculation">₱<?php echo htmlspecialchars($order['product_price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></span>

                        <div class="order-data"><strong>Quantity:</strong></div>
                        <span class="calculation">x <?php echo htmlspecialchars($order['order_quantity'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></span>

                        <div class="order-data"><strong>Shipping:</strong></div>
                        <span class="calculation">+ ₱<?php echo htmlspecialchars($order['shipping_fee'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></span>

                        <div class="order-data"><strong>Total:</strong></div>
                        <span class="calculation total-row">₱<?php echo htmlspecialchars($order['total_price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <!-- Example: "Buy Again" button at the bottom (optional) -->
                <div class="buy-again-container" style="text-align: right; margin-top: 20px;">
                    <form action="../module3/productdescrption.php" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $order['product_id']; ?>">
                        <button type="submit" class="order-action-button buy-again-button">Buy Again</button>
                    </form>
                </div>

            <?php else: ?>
                <!-- For non-cancelled statuses, you can keep your old code or a different layout -->
                <!-- e.g. your existing "Processing/Shipped/Complete" layout -->
                <div class="product">
                    <img src="<?php echo htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Product Image"
                        class="product-image">

                    <div class="product-details">
                        <?php
                        // Show order details if we have a valid status
                        if (isset($order['order_status'])) {
                            echo displayOrderDetails($order, $cancellation ?? null, $return ?? null);
                        } else {
                            echo "<p>Order details are not available.</p>";
                        }
                        ?>
                    </div>
                </div>
                <div class="order-details">
                    <div class="order-info-grid">
                        <div class="order-data">
                            <strong>Order ID:</strong>
                            <?php echo htmlspecialchars($order['order_id'] ?? 'Missing order ID', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Shipping Address:</strong>
                            <?php echo htmlspecialchars($order['address'] ?? 'Missing address', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Payment Method:</strong>
                            <?php echo htmlspecialchars($order['payment_method'] ?? 'Missing payment method', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Contact Number:</strong>
                            <?php echo htmlspecialchars($order['phone'] ?? 'Missing contact number', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <div class="order-data">
                            <strong>Product Name:</strong>
                            <?php echo htmlspecialchars($order['product_name'] ?? 'Missing product name', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>

                    <div class="order-calculations">
                        <div class="order-data"><strong>Price:</strong></div>
                        <span class="calculation">₱<?php echo htmlspecialchars($order['product_price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></span>

                        <div class="order-data"><strong>Quantity:</strong></div>
                        <span class="calculation">x <?php echo htmlspecialchars($order['order_quantity'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></span>

                        <div class="order-data"><strong>Shipping:</strong></div>
                        <span class="calculation">+ ₱<?php echo htmlspecialchars($order['shipping_fee'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></span>

                        <div class="order-data"><strong>Total:</strong></div>
                        <span class="calculation total-row">₱<?php echo htmlspecialchars($order['total_price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
        </div>
    </section>
<?php endif; ?>

</div>
</section>

</body>

</html>