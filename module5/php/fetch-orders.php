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
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                                <button type="submit" class="order-action-button view-details-button">View Details</button>
                             </form>';
            $actions .= '<form action="order-complete.php" method="POST" style="display:inline;">
                             <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                             <button type="submit" class="order-action-button receive-order-button" style="width:auto;">Order Received</button>
                          </form>';
            break;

        case 'complete':
        case 'completed':
            // Return, View Details, and To Review buttons for completed orders
            $expirationTime = strtotime($order['received_date'] . ' + ' . $order['expiration_return_date'] . ' days');
            if ($expirationTime > time()) {
                $actions .= '<form action="order-return-request.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                                <button type="submit" class="order-action-button return-order-button">Return</button>
                             </form>';
            } else {
                $actions .= '<button class="order-action-button return-order-button" disabled>Return Expired</button>';
            }

            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';
            $actions .= '<form action="order-review.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button to-review-button">To Review</button>
                         </form>';
            break;

        case 'cancelled':
            // View Details button for cancelled orders
            $actions .= '<form action="order-details.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="' . $order['order_id'] . '">
                            <button type="submit" class="order-action-button view-details-button">View Details</button>
                         </form>';
            break;

        case 'return':
            // View Details button for return orders
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
                    <?php echo htmlspecialchars(getDisplayStatus($order['order_status'])); ?>
                </p>
                <p>
                    Date Ordered: <?php echo date('d M Y', strtotime($order['received_date'] ?? $order['date_ordered'])); ?>
                </p>

            </div>

            <div class="product">
                <?php
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