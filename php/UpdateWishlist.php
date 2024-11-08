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

// Get the cart item details from the request
$user_id = $_POST['user_id']; 
$product_id = $_POST['product_id']; 

// Validate product ID
if (!isset($product_id)) {
    die("Product ID is required.");
}

// Check if the product already exists in the wishlist for the user
$sql_check = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Product already exists in the wishlist, so we remove it
    $sql_delete = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $user_id, $product_id);

    if ($stmt_delete->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product removed from wishlist']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove product from wishlist']);
    }

    $stmt_delete->close();
} else {
    // Product doesn't exist in the wishlist, insert it
    $sql_insert = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $user_id, $product_id);

    if ($stmt_insert->execute()) {
        // Successfully added to wishlist
        echo json_encode(['success' => true, 'message' => 'Product added to wishlist']);
    } else {
        // Failed to add to wishlist
        echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist']);
    }

    $stmt_insert->close();
}

$stmt_check->close();
$conn->close();
?>
