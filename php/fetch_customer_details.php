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

// Define the SQL query
$sql = "
SELECT u.user_id,u.image, u.name, u.email, u.contact, u.address, COALESCE(SUM(oi.quantity), 0) AS ordered_quantity, GROUP_CONCAT(p.product_id) AS product_ids, GROUP_CONCAT(p.name) AS product_names, GROUP_CONCAT(p.price) AS product_prices FROM users u LEFT JOIN orders o ON u.user_id = o.user_id LEFT JOIN order_items oi ON o.order_id = oi.order_id LEFT JOIN products p ON oi.product_id = p.product_id GROUP BY u.user_id,u.image,u.name, u.email, u.contact, u.address, o.order_id, o.created_at,oi.product_id ORDER BY u.user_id";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

$users = [];

// Fetch results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Add each user to the array
    }
}

// Return response as JSON
echo json_encode(['success' => true, 'users' => $users]);

$conn->close();
?>
