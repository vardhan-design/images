<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

// Extract data from JSON
$userId = isset($data['userId']) ? $data['userId'] : null; // Ensure userId is set
$cart = isset($data['cart']) ? $data['cart'] : null; // Check if cart parameter is present
$wishlist = isset($data['wishlist']) ? $data['wishlist'] : null; // Check if wishlist parameter is present

// Initialize variables to hold existing values
$existingCart = '';
$existingWishlist = '';

// Determine if cart or wishlist needs to be processed
if ($cart !== null) {
    // Fetch existing cart value for the user
    $sql = "SELECT cart FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($item = $result->fetch_assoc()) {
        $existingCart = $item['cart'] ?? ''; // Use null coalescing operator
    }
} elseif ($wishlist !== null) {
    // Fetch existing wishlist value for the user
    $sql = "SELECT wishlist FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($item = $result->fetch_assoc()) {
        $existingWishlist = $item['wishlist'] ?? ''; // Use null coalescing operator
    }
}

// Function to update cart or wishlist
function updateList($existingList, $productId) {
    $itemArray = !empty($existingList) ? explode(',', $existingList) : [];

    // Check if the product is already in the list
    if (in_array($productId, $itemArray)) {
        // Remove product from the array
        $key = array_search($productId, $itemArray);
        unset($itemArray[$key]);
        return ['updatedList' => implode(',', $itemArray), 'message' => 'removed'];
    } else {
        // Add product to the array
        $itemArray[] = $productId; 
        return ['updatedList' => implode(',', $itemArray), 'message' => 'added'];
    }
}

// Process cart if provided
if ($cart !== null) {
    // Check if the product ID already exists in the existing cart
    if (in_array($cart, explode(',', $existingCart))) {
        // Remove product from the cart
        $result = updateList($existingCart, $cart);
        $updatedCart = $result['updatedList'];

        $sql = "UPDATE users SET cart = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $updatedCart, $userId);
        
        // Execute query and return response
        if ($stmt->execute()) {
            // Count products in the cart
            $count = count(explode(',', $updatedCart));
            echo json_encode(['success' => true, 'message' => 'removed from cart', 'type' => 'cart', 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update cart']);
        }
    } else {
        // Add product to the cart
        $result = updateList($existingCart, $cart);
        $updatedCart = $result['updatedList'];

        $sql = "UPDATE users SET cart = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $updatedCart, $userId);
        
        // Execute query and return response
        if ($stmt->execute()) {
            // Count products in the cart
            $count = count(explode(',', $updatedCart));
            echo json_encode(['success' => true, 'message' => 'added to cart', 'type' => 'cart', 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update cart']);
        }
    }
}

// Process wishlist if provided
if ($wishlist !== null) {
    // Check if the product ID already exists in the existing wishlist
    if (in_array($wishlist, explode(',', $existingWishlist))) {
        // Remove product from the wishlist
        $result = updateList($existingWishlist, $wishlist);
        $updatedWishlist = $result['updatedList'];

        $sql = "UPDATE users SET wishlist = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $updatedWishlist, $userId);
        
        // Execute query and return response
        if ($stmt->execute()) {
            // Count products in the wishlist
            $count = count(explode(',', $updatedWishlist));
            echo json_encode(['success' => true, 'message' => 'removed from wishlist', 'type' => 'wishlist', 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update wishlist']);
        }
    } else {
        // Add product to the wishlist
        $result = updateList($existingWishlist, $wishlist);
        $updatedWishlist = $result['updatedList'];

        $sql = "UPDATE users SET wishlist = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $updatedWishlist, $userId);
        
        // Execute query and return response
        if ($stmt->execute()) {
            // Count products in the wishlist
            $count = count(explode(',', $updatedWishlist));
            echo json_encode(['success' => true, 'message' => 'added to wishlist', 'type' => 'wishlist', 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update wishlist']);
        }
    }
}

// If neither cart nor wishlist is provided
if ($cart === null && $wishlist === null) {
    echo json_encode(['success' => false, 'error' => 'No cart or wishlist data provided']);
}

$stmt->close();
$conn->close();
?>
