<?php
session_start(); // Start the session to access user ID

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the user is logged in and their user_id is stored in the session
$user_id = $_SESSION['user_id'] ?? null; // Replace with actual user ID retrieval

if (!$user_id) {
    die("User is not logged in.");
}

// Get the product ID to remove from the request
$product_id = $_POST['product_id']; // The product ID to be removed

// Remove the product from the cart
$sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    // Successfully removed the item
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}

$stmt->close();
$conn->close();
?>
