<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data
$product_id = $data['product_id'] ?? null;
$product_name = $data['product_name'] ?? null;
$category_name = $data['category_name'] ?? null;
$price = isset($data['price']) ? (float)$data['price'] : null;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : null;
$image_url = $data['image_url'] ?? null;
$image2 = $data['image2'] ?? null;
$image3 = $data['image3'] ?? null;
$image4 = $data['image4'] ?? null;

// Basic validation
if (!$product_id || !$product_name || !$category_name || $price === null || $quantity === null) {
    die(json_encode(['success' => false, 'message' => 'Missing required fields']));
}

// Fetch category_id based on category_name
$categoryQuery = "SELECT category_id FROM categories WHERE category_name = ?";
$categoryStmt = $conn->prepare($categoryQuery);
if ($categoryStmt) {
    $categoryStmt->bind_param("s", $category_name);
    $categoryStmt->execute();
    $categoryStmt->bind_result($category_id);
    $categoryStmt->fetch();
    $categoryStmt->close();
    
    // Check if category_id was found
    if (!$category_id) {
        die(json_encode(['success' => false, 'message' => 'Category not found']));
    }
} else {
    die(json_encode(['success' => false, 'message' => 'Error in fetching category ID: ' . $conn->error]));
}

// Update product_inventory table
$inventoryQuery = "UPDATE product_inventory SET quantity = ? WHERE product_id = ?";
$inventoryStmt = $conn->prepare($inventoryQuery);
if ($inventoryStmt) {
    $inventoryStmt->bind_param("ii", $quantity, $product_id);
    if (!$inventoryStmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error updating product inventory: ' . $inventoryStmt->error]));
    }
    $inventoryStmt->close();
} else {
    die(json_encode(['success' => false, 'message' => 'Error in preparing inventory update query: ' . $conn->error]));
}

// Update product details
$productQuery = "UPDATE products SET name = ?, price = ?, image_url = ?, image2 = ?, image3 = ?, image4 = ?, category_id = ? WHERE product_id = ?";
$productStmt = $conn->prepare($productQuery);
if ($productStmt) {
    $productStmt->bind_param("sdssssii", $product_name, $price, $image_url, $image2, $image3, $image4, $category_id, $product_id);
    if (!$productStmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error updating product: ' . $productStmt->error]));
    }
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    $productStmt->close();
} else {
    die(json_encode(['success' => false, 'message' => 'Error in preparing product update query: ' . $conn->error]));
}

$conn->close();
?>
