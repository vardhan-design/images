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
$product_id = $_POST['product_id']; // The product ID whose quantity is being updated
$new_quantity = $_POST['quantity']; // New quantity for the cart item

// Validate new quantity
if ($new_quantity <= 0) {
    die("Invalid quantity.");
}

// Check if the product already exists in the cart
$sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

// If the product is found in the cart, update its quantity
if ($result->num_rows > 0) {
    // Fetch the existing quantity
    $row = $result->fetch_assoc();
    $existing_quantity = $row['quantity'];

    // Update the cart with the new quantity
    $updated_quantity = $existing_quantity + $new_quantity; // Update or adjust the quantity as needed
    $sql_update = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $updated_quantity, $user_id, $product_id);

    if ($stmt_update->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    }

    $stmt_update->close();
} else {
    // If the product is not found, insert it into the cart
    $sql_insert = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $user_id, $product_id, $new_quantity);

    if ($stmt_insert->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }

    $stmt_insert->close();
}

$stmt->close();
$conn->close();
?>
