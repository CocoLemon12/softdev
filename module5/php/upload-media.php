<?php
include 'database.php';

$return_id = isset($_POST['return_id']) ? $_POST['return_id'] : uniqid(); // Get the return_id or generate a unique id

// Check if file exists
if (!isset($_FILES["fileUpload"]["tmp_name"])) {
    echo json_encode(["success" => false, "message" => "No file uploaded."]);
    exit();
}

// Get file content
$file_content = file_get_contents($_FILES["fileUpload"]["tmp_name"]);

// Check file size (5MB max)
if ($_FILES["fileUpload"]["size"] > 5000000) {
    echo json_encode(["success" => false, "message" => "File size exceeds 5MB limit."]);
    exit();
}

echo json_encode(["success" => true, "media_content" => base64_encode($file_content), "return_id" => $return_id]);

$conn->close();
?>
