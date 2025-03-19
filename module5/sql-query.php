<?php
include 'php/database.php';

$message = '';
$results = [];
$table_name = ''; // To hold the name of the displayed table

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $_POST['query'] ?? '';

    // Handle the "Show" buttons first
    if (isset($_POST['show_orders'])) {
        $query = "SELECT * FROM tbl_orders";
        $table_name = "tbl_orders";
    } elseif (isset($_POST['show_cancellations'])) {
        $query = "SELECT * FROM tbl_cancellations";
        $table_name = "tbl_cancellations";
    } elseif (isset($_POST['show_returns'])) {
        $query = "SELECT * FROM tbl_returns";
        $table_name = "tbl_returns";
    } elseif (isset($_POST['show_users'])) {
        $query = "SELECT * FROM tbl_users";
        $table_name = "tbl_users";
    } elseif (isset($_POST['show_products'])) {
        $query = "SELECT * FROM products";
        $table_name = "products";
    } elseif (isset($_POST['show_tables'])) {
        $query = "SELECT TABLE_NAME, TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()";
        $table_name = "Tables Information";
    }

    // Only run if $query is not empty
    if (!empty($query)) {
        // Check the type of query
        $lowerQuery = strtolower(trim($query));
        if (strpos($lowerQuery, 'select') === 0) {
            // SELECT query
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $results = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $message = "0 results or error in SELECT.";
            }
        } elseif (strpos($lowerQuery, 'insert') === 0) {
            // INSERT query
            if ($conn->query($query) === TRUE) {
                $message = "New record(s) created successfully";
            } else {
                $message = "Error: " . $query . "<br>" . $conn->error;
            }
        } elseif (strpos($lowerQuery, 'update') === 0) {
            // UPDATE query
            if ($conn->query($query) === TRUE) {
                $message = "Record updated successfully";
            } else {
                $message = "Error updating record: " . $query . "<br>" . $conn->error;
            }
        } elseif (strpos($lowerQuery, 'delete') === 0) {
            // DELETE query
            if ($conn->query($query) === TRUE) {
                $message = "Record deleted successfully";
            } else {
                $message = "Error deleting record: " . $query . "<br>" . $conn->error;
            }
        } elseif (strpos($lowerQuery, 'create table') === 0) {
            // CREATE TABLE
            if ($conn->query($query) === TRUE) {
                $message = "Table created successfully";
            } else {
                $message = "Error creating table: " . $query . "<br>" . $conn->error;
            }
        } elseif (strpos($lowerQuery, 'alter table') === 0) {
            // ALTER TABLE
            if ($conn->query($query) === TRUE) {
                $message = "Table altered successfully";
            } else {
                $message = "Error altering table: " . $query . "<br>" . $conn->error;
            }
        } elseif (strpos($lowerQuery, 'drop table') === 0) {
            // DROP TABLE
            if ($conn->query($query) === TRUE) {
                $message = "Table dropped successfully";
            } else {
                $message = "Error dropping table: " . $query . "<br>" . $conn->error;
            }
        } else {
            $message = "Only SELECT, INSERT, UPDATE, DELETE, CREATE TABLE, DROP TABLE, and ALTER TABLE queries are allowed.";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Query</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/sql-query.css">
    <style>
        .results-grid {
            max-height: 600px;
            overflow-y: auto;
        }

        .results-grid table {
            border-collapse: collapse;
            width: 100%;
        }

        .results-grid th,
        .results-grid td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .results-grid th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <section class="section">
        <div class="content">
            <h1>SQL Query</h1>
            <form method="POST" action="">
                <textarea name="query" rows="4" cols="50" placeholder="Enter your SQL query here..."></textarea>
                <br>
                <button type="submit">Execute</button>
                <button type="submit" name="show_tables">Show Tables</button>
                <button type="submit" name="show_orders">Show Orders</button>
                <button type="submit" name="show_cancellations">Show Cancellations</button>
                <button type="submit" name="show_returns">Show Returns</button>
                <button type="submit" name="show_users">Show Users</button>
                <button type="submit" name="show_products">Show Products</button>
            </form>
            <p><?php echo $message; ?></p>
            <?php if (!empty($results)): ?>
                <div class="results-grid">
                    <h2><?php echo isset($table_name) ? htmlspecialchars($table_name) : 'Results'; ?></h2>
                    <table>
                        <thead>
                            <tr>
                                <?php foreach (array_keys($results[0]) as $header): ?>
                                    <th><?php echo htmlspecialchars($header); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?php echo htmlspecialchars($cell); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>

</html>