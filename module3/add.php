<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .container {
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h2>Add New Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="product_name" required><br>
        <label>Brand:</label>
        <input type="text" name="product_brand" required><br>
        <label>Type (Category):</label>
        <input type="text" name="product_type" required><br>
        <label>Stock:</label>
        <input type="number" name="product_stock" required><br>
        <label>Price (₱):</label>
        <input type="number" step="0.01" name="product_price" required><br>
        <label>Description:</label>
        <textarea name="product_description"></textarea><br>
        <label>Specification:</label>
        <textarea name="product_specification"></textarea><br>
        <label>Image 1:</label>
        <input type="file" name="product_img"><br>
        <label>Image 2:</label>
        <input type="file" name="product_img2"><br>
        <label>Image 3:</label>
        <input type="file" name="product_img3"><br>
        <label>Image 4:</label>
        <input type="file" name="product_img4"><br>
        <label>Image 5:</label>
        <input type="file" name="product_img5"><br>
        <button type="submit" name="submit">Add Product</button>
    </form>

    <?php
    // Database Connection
    $host = "localhost";
    $username = "pgfbzuml_miah";
    $password = "Miah@2025!";
    $database = "pgfbzuml_test";
    
    $conn = new mysqli($host, $username, $password, $database);

    if (isset($_POST['submit'])) {
        $name = $_POST['product_name'];
        $brand = $_POST['product_brand'];
        $type = $_POST['product_type'];
        $stock = $_POST['product_stock'];
        $price = $_POST['product_price'];
        $description = $_POST['product_description'];
        $specification = $_POST['product_specification'];
        $target_dir = "productimages/";
        $image_urls = [];
    
        for ($i = 1; $i <= 5; $i++) {
            $file_input = "product_img" . ($i == 1 ? "" : $i);
            if (!empty($_FILES[$file_input]['name'])) {
                $image_name = basename($_FILES[$file_input]['name']);
                $target_file = $target_dir . $image_name;
                if (move_uploaded_file($_FILES[$file_input]['tmp_name'], $target_file)) {
                    $image_urls[] = $target_file;
                }
            }
        }
    
        // Convert array to JSON
        $image_json = json_encode($image_urls);
    
        $sql = "INSERT INTO products 
            (product_name, product_brand, product_category, product_stock, product_price, product_description, product_specification, product_images) 
            VALUES 
            ('$name', '$brand', '$type', $stock, $price, '$description', '$specification', '$image_json')";
    
        if ($conn->query($sql) === TRUE) {
            echo "<p>Product added successfully!</p>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }
    }
    
    ?>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Type</th>
                    <th>Stock</th>
                    <th>Price (₱)</th>
                    <th>Description</th>
                    <th>Specification</th>
                    <th>Image</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php
               $sql = "SELECT * FROM products";
               $result = $conn->query($sql);
               
               if ($result->num_rows > 0) {
                   while ($row = $result->fetch_assoc()) {
                       echo "<tr>
                           <td>{$row['product_id']}</td>
                           <td>{$row['product_name']}</td>
                           <td>{$row['product_brand']}</td>
                           <td>{$row['product_category']}</td>
                           <td>{$row['product_stock']}</td>
                           <td>₱" . number_format($row['product_price'], 2) . "</td>
                           <td>{$row['product_description']}</td>
                           <td>{$row['product_specification']}</td>
                           <td>";
               
                       // Decode JSON images
                       $image_urls = json_decode($row['product_images'], true);
                       if (!empty($image_urls)) {
                           foreach ($image_urls as $img) {
                               echo "<img src='$img' alt='Product Image'>";
                           }
                       }
               
                       echo "</td>
                           <td>" . ($row['rating'] ?? 'N/A') . "</td>
                       </tr>";
                   }
               } else {
                   echo "<tr><td colspan='10'>No products found</td></tr>";
               }
               
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>