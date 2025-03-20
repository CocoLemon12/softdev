<?php
include 'database.php';

$user_id = 1; // For testing (replace with session-based user ID in production)
$status_filter = isset($_GET['status']) ? strtolower($_GET['status']) : 'all';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// 1. Count total orders for pagination
$total_query = "SELECT COUNT(*) as total FROM tbl_orders WHERE user_id = ?";
if ($status_filter !== 'all' && $status_filter !== 'order history') {
    $total_query .= " AND LOWER(order_status) = ?";
} elseif ($status_filter === 'all') {
    $total_query .= " AND LOWER(order_status) IN ('processing', 'shipped')";
} elseif ($status_filter === 'order history') {
    $total_query .= " AND LOWER(order_status) IN ('complete', 'cancelled', 'return')";
}

$stmt = $conn->prepare($total_query);
if ($status_filter === 'all' || $status_filter === 'order history') {
    $stmt->bind_param("i", $user_id);
} else {
    $stmt->bind_param("is", $user_id, $status_filter);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_orders = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit);
$stmt->close();

// 2. Fetch orders for the user with pagination & status filter
$query = "SELECT tbl_orders.*, products.product_name, products.product_images
          FROM tbl_orders
          JOIN products ON tbl_orders.product_id = products.product_id
          WHERE tbl_orders.user_id = ? ";

if ($status_filter !== 'all' && $status_filter !== 'order history') {
    $query .= "AND LOWER(tbl_orders.order_status) = ? ";
} elseif ($status_filter === 'all') {
    $query .= "AND LOWER(tbl_orders.order_status) IN ('processing', 'shipped') ";
} elseif ($status_filter === 'order history') {
    $query .= "AND LOWER(tbl_orders.order_status) IN ('complete', 'cancelled', 'return') ";
}

$query .= "ORDER BY COALESCE(tbl_orders.received_date, tbl_orders.date_ordered) DESC
           LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

if ($status_filter === 'all' || $status_filter === 'order history') {
    $stmt->bind_param("iii", $user_id, $limit, $offset);
} else {
    $stmt->bind_param("isii", $user_id, $status_filter, $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($order = $result->fetch_assoc()) {
    // Hardcoded return period & warranty
    $order['return_period'] = '5 days Return';
    $order['warranty']      = '7 days local supplier warranty';
    $orders[] = $order;
}
$stmt->close();
$conn->close();

/**
 * Map DB status to display text.
 */
function getDisplayStatus($status)
{
    $lower = strtolower(trim($status));
    if ($lower === 'shipped') {
        return 'In Transit';
    } elseif ($lower === 'processing') {
        return 'Your order is processing.';
    } elseif ($lower === 'complete' || $lower === 'completed') {
        return 'Order Successfully Completed';
    } elseif ($lower === 'cancelled') {
        return 'Your order has been successfully cancelled';
    } elseif ($lower === 'return') {
        return 'Your return is being processed.';
    } else {
        return $status;
    }
}

/**
 * Map DB status to a status icon path.
 */
function getStatusImage($status)
{
    switch (strtolower($status)) {
        case 'processing':
            return 'assets/processing.png';
        case 'shipped':
            return 'assets/shipping.png';
        case 'complete':
            return 'assets/delivered.png';
        case 'cancelled':
            return 'assets/cancelled.png';
        case 'pending':
            return 'assets/pending.png';
        default:
            return 'assets/default-status.png';
    }
}

/**
 * Generate action buttons depending on order status.
 */
function getOrderActions($order)
{
    $actions = '';
    $status  = strtolower($order['order_status']);

    switch ($status) {
        case 'processing':
            // Processing: View Details + Cancel
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';
            $actions .= '<form action="order-cancellation-request.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button cancel-button">Cancel Order</button>
                         </form>';
            break;

        case 'shipped':
            // Shipped: View Details + Mark Received
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';
            $actions .= '<form action="mark-order-complete.php" method="POST" style="display:inline;">
                             <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                             <button type="submit" class="order-action-button receive-order-button" style="width:auto;">
                                 Order Received
                             </button>
                         </form>';
            break;

        case 'complete':
        case 'completed':
            // Completed: Return/Refund (if within window), View Details, and "To Review" => modal
            $expirationTime = strtotime($order['received_date'] . ' + ' . $order['expiration_return_date'] . ' days');
            if ($expirationTime > time()) {
                $actions .= '<form action="order-return-request.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                                <button type="submit" class="order-action-button return-order-button">Return/Refund</button>
                             </form>';
            } else {
                $actions .= '<button class="order-action-button return-order-button" disabled>Return Expired</button>';
            }

            // View Details
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';

            // "To Review" => Open a modal, pass product name & image as data attributes
            // We'll escape the product_name and image for JS use.
            $escapedName  = htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8');
            // Decode the product image
            $productImages = json_decode($order['product_images'], true);
            $firstImage = isset($productImages[0]) ? "../module3/" . $productImages[0] : "../module5/assets/default-product.png";
            $escapedImg   = htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8');

            $actions .= '<button type="button"
                                class="order-action-button to-review-button"
                                data-order-id="' . $order['order_id'] . '"
                                data-product-name="' . $escapedName . '"
                                data-product-img="' . $escapedImg . '"
                                onclick="openReviewModal(this)">
                            To Review
                         </button>';
            break;

        case 'cancelled':
            // Cancelled: just View Details
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';
            break;

        case 'return':
            // Return: just View Details
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';
            break;

        default:
            // No additional action buttons for other statuses
            break;
    }

    return $actions;
}
?>
<!-- Output the orders (no extra container) -->
<?php if (empty($orders)): ?>
    <div class="no-orders">
        <img src="assets/no-orders.png">
        <p>No orders data can be found</p>
        <button onclick="window.location.href='../module3/'" class="order-actions-button">Continue Shopping</button>
    </div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div class="order-container">
            <div class="above">
                <p>
                    <img src="<?php echo getStatusImage($order['order_status']); ?>" alt="Status Icon">

                    <?php if (strtolower($order['order_status']) === 'shipped'): ?>
                        In transit - Estimated Delivery by
                        <strong><?php echo date('d M Y', strtotime($order['order_arrival'])); ?></strong>
                    <?php else: ?>
                        <?php echo htmlspecialchars(getDisplayStatus($order['order_status'])); ?>
                    <?php endif; ?>
                </p>
                <p>
                    Date Ordered:
                    <strong><?php echo date('d M Y', strtotime($order['received_date'] ?? $order['date_ordered'])); ?></strong>
                </p>
            </div>

            <div class="product">
                <?php
                // decode product image
                $productImages = json_decode($order['product_images'], true);
                $firstImage = isset($productImages[0]) ? "../module3/" . $productImages[0] : "../module5/assets/default-product.png";
                ?>
                <img src="<?php echo htmlspecialchars($firstImage, ENT_QUOTES, 'UTF-8'); ?>"
                    alt="<?php echo htmlspecialchars($order['product_name'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="product-image">

                <div class="product-details">
                    <p class="product-name"><?php echo htmlspecialchars($order['product_name']); ?></p>

                    <!-- 5 Days Return & 7 Days Local Supplier Warranty -->
                    <div class="return-warranty-container">
                        <span class="return-warranty">5 days Free Return</span>
                        <span class="return-warranty">7 days local supplier warranty</span>
                    </div>
                </div>
            </div>

            <div class="price-info">
                <div class="total-info">
                    <p class="item-info">
                        Total(<?php echo $order['order_quantity'] > 1 ? $order['order_quantity'] . ' Items' : '1 Item'; ?>):
                    </p>
                    <p class="total-price">
                        ₱<?php echo number_format($order['total_price'], 2); ?>
                    </p>
                </div>
            </div>

            <div class="order-actions">
                <?php echo getOrderActions($order); ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="pagination">
    <!-- Previous page link -->
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>" class="prev">Previous</a>
    <?php else: ?>
        <span class="prev disabled">Previous</span>
    <?php endif; ?>

    <!-- Page number links -->
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <!-- Next page link -->
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>" class="next">Next</a>
    <?php else: ?>
        <span class="next disabled">Next</span>
    <?php endif; ?>
</div>

<!-- (B) Modal HTML Markup -->
<div id="reviewModalOverlay" onclick="closeReviewModal()"></div>
<div id="reviewModal">
    <div class="review-modal-header">
        <span class="review-modal-close" onclick="closeReviewModal()">&lt;</span>
        <h2>Write Review</h2>
    </div>

    <!-- Product info will be updated dynamically -->
    <div class="review-product-info">
        <img id="reviewProductImage" src="assets/default-product.png" alt="Product" class="review-product-img">
        <p id="reviewProductName"></p>
    </div>

    <!-- Rating row container -->
    <div class="review-stars-row">
        <!-- Label on the left -->
        <span class="rating-label">General Rating:</span>

        <!-- Star container on the right -->
        <div class="review-stars">
            <span class="star" data-value="1" onmouseover="hoverStars(1)" onmouseout="resetStars()" onclick="setStarRating(1)">&#9733;</span>
            <span class="star" data-value="2" onmouseover="hoverStars(2)" onmouseout="resetStars()" onclick="setStarRating(2)">&#9733;</span>
            <span class="star" data-value="3" onmouseover="hoverStars(3)" onmouseout="resetStars()" onclick="setStarRating(3)">&#9733;</span>
            <span class="star" data-value="4" onmouseover="hoverStars(4)" onmouseout="resetStars()" onclick="setStarRating(4)">&#9733;</span>
            <span class="star" data-value="5" onmouseover="hoverStars(5)" onmouseout="resetStars()" onclick="setStarRating(5)">&#9733;</span>
        </div>
    </div>


    <!-- Hidden fields to store rating or order ID -->
    <input type="hidden" id="reviewOrderId" value="">
    <input type="hidden" id="reviewRating" value="0">

    <!-- Textarea for user feedback -->
    <label for="reviewText" style="display:block; margin-top:10px; font-size: 20px; font-weight: 500;">Write Review</label>
    <textarea id="reviewText" rows="4" style="width:100%; min-height: 340px;" placeholder="Your feedback is invaluable to us! Kindly write your feedback below."></textarea>


    <!-- Submit button -->
    <div class="review-submit-container">
        <button class="review-submit-btn" onclick="submitReview()">Submit</button>
    </div>
</div>

<!-- (C) JavaScript to open/close modal, set star rating, etc. -->
<script>
    function openReviewModal(button) {
        // 'button' is the DOM element with data attributes
        const orderId = button.getAttribute('data-order-id');
        const productName = button.getAttribute('data-product-name');
        const productImage = button.getAttribute('data-product-img');

        // Show overlay & modal
        document.getElementById('reviewModalOverlay').style.display = 'block';
        document.getElementById('reviewModal').style.display = 'block';

        // Store orderId in hidden field
        document.getElementById('reviewOrderId').value = orderId;

        // Reset rating & text
        document.getElementById('reviewRating').value = 0;
        document.getElementById('reviewText').value = '';

        // Clear star selection
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => star.classList.remove('selected'));

        // Populate product name & image
        document.getElementById('reviewProductName').textContent = productName;
        document.getElementById('reviewProductImage').src = productImage;
    }

    function closeReviewModal() {
        document.getElementById('reviewModalOverlay').style.display = 'none';
        document.getElementById('reviewModal').style.display = 'none';
    }

    function setStarRating(value) {
        document.getElementById('reviewRating').value = value;
        // highlight stars
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            const starValue = parseInt(star.getAttribute('data-value'), 10);
            if (starValue <= value) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }

    function submitReview() {
        const orderId = document.getElementById('reviewOrderId').value;
        const rating = document.getElementById('reviewRating').value;
        const text = document.getElementById('reviewText').value.trim();

        // For now, just close modal & alert
        closeReviewModal();
        alert("Review submitted for Order ID " + orderId +
            "\nRating: " + rating +
            "\nComment: " + text);

        // If you want to do an AJAX call to store in DB, do it here
        // e.g., fetch('save-review.php', { ... })
    }

    function hoverStars(value) {
        const stars = document.querySelectorAll('.review-stars .star');
        stars.forEach(star => {
            if (parseInt(star.getAttribute('data-value'), 10) <= value) {
                star.classList.add('hovered');
            } else {
                star.classList.remove('hovered');
            }
        });
    }

    function resetStars() {
        const stars = document.querySelectorAll('.review-stars .star');
        stars.forEach(star => star.classList.remove('hovered'));
    }
</script>