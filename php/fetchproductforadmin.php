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

// Define the SQL query to join both product_inventory and order_items
$sql = "SELECT p.product_id, p.name, p.price, c.category_id, c.category_name, 
               pi.quantity AS inventory_quantity, 
               COALESCE(SUM(oi.quantity), 0) AS ordered_quantity,
               p.image_url, r.review_text, r.rating
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_inventory pi ON p.product_id = pi.product_id
        LEFT JOIN order_items oi ON p.product_id = oi.product_id
        LEFT JOIN reviews r ON p.product_id = r.product_id
        GROUP BY p.product_id, p.name, p.price, c.category_id, c.category_name, 
                 pi.quantity, p.image_url, r.review_text, r.rating";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

$products = [];

// Fetch results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        
        // If this product hasn't been added yet, initialize it
        if (!isset($products[$productId])) {
            $products[$productId] = [
                'id' => $productId,
                'name' => $row['name'],
                'price' => $row['price'],
                'category' => $row['category_name'], // Use the correct field
                'inventory_quantity' => $row['inventory_quantity'], // Quantity from inventory
                'ordered_quantity' => $row['ordered_quantity'], // Quantity from orders
                'status' => ($row['ordered_quantity'] > 0) ? 'Ordered' : 'Active', // Set status based on ordered quantity
                'image_url' => $row['image_url'],
                'reviews' => [] // Initialize reviews array for each product
            ];
        }

        // Append each review if available
        if ($row['review_text'] !== null) {
            $products[$productId]['reviews'][] = [
                'text' => $row['review_text'],
                'rating' => $row['rating']
            ];
        }
    }
}

// Re-index to ensure a proper JSON array
echo json_encode(['success' => true, 'products' => array_values($products)]);
$conn->close();
?>
