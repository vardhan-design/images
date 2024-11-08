<?php
session_start(); // Start the session to access the user ID (if using session-based login)

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming user is logged in and their user_id is stored in the session
$user_id = $_SESSION['user_id'] ?? null; // Replace with actual user ID retrieval method

if (!$user_id) {
    // If the user is not logged in, show an error or handle accordingly
    die("User is not logged in.");
}

// Query to fetch cart items for the user
$sql = "SELECT c.cart_id, p.*,c.quantity, (p.price * c.quantity) AS total_price 
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // 'i' stands for integer type
$stmt->execute();

$result = $stmt->get_result();

// Fetch all cart items
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the cart items as a JSON response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'cart_items' => $cart_items]);
?>
