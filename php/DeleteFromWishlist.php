<?php
session_start(); // Start the session to access user ID

// Include the database connection
require 'db_connection.php'; // Assuming this file contains your DB connection setup

// Assuming user_id is stored in session when logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not logged in.");
}

// Get the product ID from the POST request
$product_id = $_POST['product_id']; 

// Validate product ID
if (!isset($product_id)) {
    die("Product ID is required.");
}

// Delete the product from the wishlist
$sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product removed from wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove product from wishlist']);
}

$stmt->close();
$conn->close();
?>
