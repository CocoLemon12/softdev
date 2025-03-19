<?php
  ini_set('display_errors', 1);

$host = "localhost";
$username = "pgfbzuml_miah";
$password = "Miah@2025!";
$database = "pgfbzuml_test";

// Create MySQLi connection
$mysqli = new mysqli($host, $username, $password, $database);

// Check connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>