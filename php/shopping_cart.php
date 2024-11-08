<?php
header('Content-Type: text/html');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Get user ID from query parameters
$userId = $_GET['userId'] ?? '';
if (empty($userId)) {
    echo json_encode(['error' => 'User ID is required']);
    exit();
}

// Prepare and execute the query to fetch product IDs from the user's cart
$stmt = $conn->prepare("SELECT product_id FROM users WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

// Ensure user_id is treated as an integer
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$productIds = [];
if ($item = $result->fetch_assoc()) {
    // Log the fetched product_id to check its format
    error_log("Fetched product_id: " . $item['product_id']); 

    // Clean the fetched product_id if necessary
    $cleanedProductId = trim($item['product_id'], "[]"); // Remove brackets if they exist
    $productIds = array_map('trim', explode(',', $cleanedProductId)); 

    // Log the product IDs array
    error_log("Product IDs array: " . implode(', ', $productIds)); // Log the product IDs array
}

if (empty($productIds)) {
    echo json_encode(['items' => []]); // Return empty array if no products found
    exit();
}

// Prepare to fetch product details
$placeholders = implode(',', array_fill(0, count($productIds), '?')); // Create placeholders for binding
$productStmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
if (!$productStmt) {
    echo json_encode(['error' => 'Failed to prepare product statement: ' . $conn->error]);
    exit();
}

// Dynamically bind parameters for product IDs
$types = str_repeat('i', count($productIds)); // Assuming product IDs are integers
$productStmt->bind_param($types, ...$productIds);
$productStmt->execute();
$productResult = $productStmt->get_result();

$items = [];
while ($product = $productResult->fetch_assoc()) {
    $items[] = [
        'id' => $product['product_id'],
        'name' => $product['name'],
        'color' => $product['color_options'],
        'size' => $product['size_options'],
        'price' => $product['price'],
        'image' => $product['image_url'], // Assuming image_url is the field for product images
    ];
}

echo json_encode(['items' => $items]); // Ensure only valid JSON is output

// Close statements and connection
$productStmt->close();
$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        html, body {
            height: 100%;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        h1 {
            margin-bottom: 20px;
        }

        /* Product Section */
        .product-section {
            display: flex;
            gap: 20px;
            padding: 10px;
            border: 2px solid transparent;
            border-radius: 8px;
            transition: border-color 0.3s;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product-section:hover {
            border-color: #e63946;
            cursor: pointer;
        }
        .product-images {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .product-images img {
            max-width: 100%;
            border-radius: 8px;
            cursor: pointer;
        }
        .thumbnail {
            margin-top: 10px;
            width: 100px;
            height: 100px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }
        .product-info {
            flex: 1;
            overflow-y: auto;
            padding: 0 10px;
        }
        .product-info h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .product-info .price {
            color: #e63946;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .size-options, .color-options {
            margin: 15px 0;
        }
        .size-options label, .color-options label {
            margin-right: 10px;
            cursor: pointer;
        }
        .size-guide {
            margin: 20px 0;
        }
        .reviews {
            margin-top: 30px;
        }
        .reviews h3 {
            margin-bottom: 10px;
        }
        .review-card {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        /* Cost Summary */
        .cost-summary {
            display: flex;
            flex-direction: column;
            padding: 15px;
            border-top: 1px solid #eee;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .cost-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .promo-code {
            margin-top: 20px;
        }
        .promo-code input {
            padding: 10px;
            width: 70%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .promo-code button {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }

        /* Checkout Button */
        .checkout-btn {
            padding: 15px;
            background-color: #e63946;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        .checkout-btn:hover {
            background-color: #d62839;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.5); /* Fallback color */
            backdrop-filter: blur(5px); /* Blur background */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            border-radius: 12px; /* Curved edges */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Modal Form */
        .modal-form {
            display: flex;
            flex-direction: column;
        }
        .modal-form div {
            margin-bottom: 15px;
        }
        .modal-form label {
            margin-bottom: 5px;
        }
        .modal-form input {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .modal-form button {
            padding: 10px;
            background-color: #e63946;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .modal-form button:hover {
            background-color: #d62839;
        }
  .payment-methods {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
        }
 .payment-method {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            flex-basis: 30%;
            cursor: pointer;
            transition: background 0.3s;
        }
        .payment-method:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

<div class="container">
        <h1>Shopping Cart</h1>
        <div id="product-list"></div>

        <!-- Cost Summary -->
        <div class="cost-summary">
            <div class="cost-item">
                <span>Subtotal:</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="cost-item">
                <span>Shipping:</span>
                <span>$5.00</span>
            </div>
            <div class="cost-item">
                <span>Total:</span>
                <span id="total">$5.00</span>
            </div>
        </div>

        <!-- Promo Code -->
        <div class="promo-code">
            <input type="text" placeholder="Enter Promo Code">
            <button>Apply</button>
        </div>

        <!-- Checkout Button -->
        <button class="checkout-btn" onclick="openModal()">Proceed to Checkout</button>
    </div>

    <!-- Modal for Checkout -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Checkout</h2>
            <form id="checkoutForm" class="modal-form">
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>
		<!-- Payment Methods -->
        <h2>Payment Methods</h2>
        <div class="payment-methods">
            <div class="payment-method">
                <h3>Credit/Debit Card</h3>
                <p>Pay with your credit or debit card.</p>
            </div>
            <div class="payment-method">
                <h3>PayPal</h3>
                <p>Pay securely using PayPal.</p>
            </div>
            <div class="payment-method">
                <h3>Bank Transfer</h3>
                <p>Transfer directly from your bank.</p>
            </div>
        </div>

                <button type="submit">Complete Purchase</button>
            </form>
        </div>
    </div>
    <script>
        console.log("Inline script loaded!");
    </script>

   

</body>
</html>
