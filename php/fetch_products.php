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
$category = $data['category'] ?? ''; 
$categoryInt = (int)$category; // Ensure it's an integer

// Prepare SQL statement
if ($categoryInt == 0) {
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
    reviews r ON p.product_id = r.product_id";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
} else {
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
WHERE 
    p.category_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    // Log and bind parameters
    error_log("Binding categoryInt: " . $categoryInt);
    $stmt->bind_param("i", $categoryInt); // 'i' means integer
}

// Execute the prepared statement
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Fetch products
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Return response as JSON
echo json_encode(['success' => true, 'products' => $products]);

// Clean up
$stmt->close();
$conn->close();
?>
