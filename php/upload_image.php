<?php
header('Content-Type: application/json');

// Define the folder for uploaded images
$targetDir = "E-commerceebPage/"; // Make sure this folder is writable and exists

// Check if the image file is uploaded correctly
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    // Generate a unique name for the file to avoid overwrites
    $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    
    // Check if the file is an image
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array(strtolower($fileType), $allowedTypes)) {
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Create the full path or URL to return to the client
            $fullPath = "http://" . $_SERVER['HTTP_HOST'] . "/" . $targetDir . $fileName;
            echo json_encode(['success' => true, 'message' => 'Image uploaded successfully', 'fullPath' => $fullPath]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save the image.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid file type.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or file upload error.']);
}
?>
