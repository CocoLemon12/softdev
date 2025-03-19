<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="icon" type="image/x-icon" href="assets/DJMLogo.png">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/orders.css">
</head>

<?php include 'php/nav-bar.php'; ?>

<body>
    <section class="section orders-section">
        <div class="content orders-content">
            <div class="orders-header">
                <h1>My Orders</h1>
            </div>

            <?php
            // Get the status filter from the URL, default to 'All' if not set
            $status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';
            ?>

            <!-- Orders Navbar -->
            <div class="orders-navbar-box">
                <div class="orders-navbar">
                    <!-- Navigation links for different order statuses -->
                    <a href="orders.php?status=All" class="<?php echo $status_filter == 'All' ? 'active' : ''; ?>">All</a>
                    <a href="orders.php?status=Processing" class="<?php echo $status_filter == 'Processing' ? 'active' : ''; ?>">Processing</a>
                    <a href="orders.php?status=Shipped" class="<?php echo $status_filter == 'Shipped' ? 'active' : ''; ?>">In Transit</a>
                    <a href="orders.php?status=Complete" class="<?php echo $status_filter == 'Complete' ? 'active' : ''; ?>">Completed</a>
                    <a href="orders.php?status=Cancelled" class="<?php echo $status_filter == 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
                    <a href="orders.php?status=Return" class="<?php echo $status_filter == 'Return' ? 'active' : ''; ?>">Return</a>
                    <a href="orders.php?status=Order History" class="<?php echo $status_filter == 'Order History' ? 'active' : ''; ?>">Order History</a>
                </div>
            </div>

            <!-- Orders Container: Fetching data from database -->
            <div id="orders-container">
                <?php
                // Assuming 'fetch-orders.php' includes fetching logic to display orders
                include 'php/fetch-orders.php';
                ?>
            </div>
        </div>
    </section>
</body>

</html>