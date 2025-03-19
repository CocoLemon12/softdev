<?php
// Database Connection
$host = "localhost";
$username = "pgfbzuml_miah";
$password = "Miah@2025!";
$database = "pgfbzuml_test";

$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Products
$sql = "SELECT product_id, product_name, product_category, product_stock, product_price, product_description FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Page</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        button { padding: 8px 12px; background-color: blue; color: white; border: none; cursor: pointer; }
        button:disabled { background-color: gray; }
    </style>
</head>
<body>

    <h1>Buy Products</h1>

    <?php if ($result->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Price</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Buy</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row["product_name"]; ?></td>
                    <td><?php echo $row["product_category"]; ?></td>
                    <td id="stock_<?php echo $row['product_id']; ?>"><?php echo $row["product_stock"]; ?></td>
                    <td><?php echo number_format($row["product_price"], 2); ?></td>
                    <td><?php echo $row["product_description"]; ?></td>
                    <td>
                        <input type="number" id="quantity_<?php echo $row['product_id']; ?>" min="1" value="1" 
                               max="<?php echo $row['product_stock']; ?>" style="width: 50px;">
                    </td>
                    <td>
                        <button onclick="buyProduct(<?php echo $row['product_id']; ?>)" 
                                id="buyBtn_<?php echo $row['product_id']; ?>" 
                                <?php echo ($row["product_stock"] == 0) ? "disabled" : ""; ?>>
                            Buy
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No products available.</p>
    <?php } ?>

    <script>
        function buyProduct(productID) {
            let quantity = document.getElementById("quantity_" + productID).value;
            
            if (quantity <= 0) {
                alert("Invalid quantity.");
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "buy_process.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        let stockElement = document.getElementById("stock_" + productID);
                        let newStock = response.newStock;
                        stockElement.textContent = newStock;

                        if (newStock == 0) {
                            document.getElementById("buyBtn_" + productID).disabled = true;
                        }

                        alert("Purchase successful!");
                    } else {
                        alert(response.message);
                    }
                }
            };

            xhr.send("productID=" + productID + "&quantity=" + quantity);
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
