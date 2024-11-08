<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Get user ID from query parameters
$userId = $_GET['userId'] ?? '';
if (empty($userId)) {
    echo json_encode(['error' => 'User ID is required']);
    exit();
}

// Prepare and execute the query to fetch product IDs from the user's cart
$stmt = $conn->prepare("SELECT wishlist FROM users WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

// Ensure user_id is treated as an integer
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$productIds = [];
if ($item = $result->fetch_assoc()) {
    // Log the fetched product_id to check its format
    error_log("Fetched product_id: " . $item['wishlist']); 

    // Clean the fetched product_id if necessary
    $cleanedProductId = trim($item['wishlist'], "[]"); // Remove brackets if they exist
    $productIds = array_map('trim', explode(',', $cleanedProductId)); 

    // Log the product IDs array
    error_log("Product IDs array: " . implode(', ', $productIds)); // Log the product IDs array
}

if (empty($productIds)) {
    echo json_encode(['items' => []]); // Return empty array if no products found
    exit();
}

// Prepare to fetch product details
$placeholders = implode(',', array_fill(0, count($productIds), '?')); // Create placeholders for binding
$productStmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
if (!$productStmt) {
    echo json_encode(['error' => 'Failed to prepare product statement: ' . $conn->error]);
    exit();
}

// Dynamically bind parameters for product IDs
$types = str_repeat('i', count($productIds)); // Assuming product IDs are integers
$productStmt->bind_param($types, ...$productIds);
$productStmt->execute();
$productResult = $productStmt->get_result();

$items = [];
while ($product = $productResult->fetch_assoc()) {
    $items[] = [
        'id' => $product['product_id'],
        'name' => $product['name'],
        'color' => $product['color_options'],
        'size' => $product['size_options'],
        'price' => $product['price'],
        'image' => $product['image_url'], // Assuming image_url is the field for product images
    ];
}

echo json_encode(['items' => $items]); // Ensure only valid JSON is output

// Close statements and connection
$productStmt->close();
$stmt->close();
$conn->close();
?>
