<?php
error_reporting(E_ERROR | E_PARSE); // Suppress warnings

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the input
$data = json_decode(file_get_contents('php://input'), true);

// Check if category is set and cast it to integer
$category = $data['productId'] ?? ''; 
$categoryInt = (int)$category; // Ensure it's an integer

// Prepare SQL statement

$sql = "SELECT 
p.*, 
i.quantity, 
r.review_text, 
r.rating
FROM 
products p
JOIN 
product_inventory i ON p.product_id = i.product_id
LEFT JOIN 
reviews r ON p.product_id = r.product_id
WHERE  p.product_id=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    error_log("Binding categoryInt: " . $categoryInt);
    $stmt->bind_param("i", $categoryInt); // 'i' means integer



// Execute the prepared statement
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Fetch products
$products = [];
while ($row = $result->fetch_assoc()) {
    $productId = $row['product_id'];
    if (!isset($products[$productId])) {
        // Initialize product with basic details (all columns from products table) and empty reviews array
        $products[] = $row; // This will include all columns from the products table
        $products[]['review_count'] = 0; // Initialize review count to 0
    }

    // Add the review to the product's review array if it exists
    if ($row['review_text'] && $row['rating']) {
        $products[$productId]['reviews'][] = [
            'review_text' => $row['review_text'],
            'rating' => (int)$row['rating'] // Rating as integer
        ];
        // Increase the review count for this product
        $products[$productId]['review_count']++;
    }
}
// Check if there are reviews
if ($row['review_text'] && $row['rating']) {
    $product['reviews'][] = [
        'review_text' => $row['review_text'],
        'rating' => (int)$row['rating'] // Rating as integer
    ];
}
// Return response as JSON
echo json_encode(['success' => true, 'products' => $products]);

// Clean up
$stmt->close();
$conn->close();
?>
