<?php
// place_order.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve data from the POST request
    $userId = $_POST['userId'];
    $amount = $_POST['amount'];
    $productIds = json_decode($_POST['productIds']); // Decode the JSON array of product IDs
    $status = 'Order Placed';
    $date = date('Y-m-d H:i:s'); // Current date and time

    // Prepare and execute the SQL query to insert the order
    $sql = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (:user_id, :amount, :status, :order_date)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_date', $date);
    $stmt->execute();
    
    // Get the last inserted order ID
    $orderId = $pdo->lastInsertId();

    // Prepare the SQL query to insert order items
    $sqlItem = "INSERT INTO order_items (order_id, product_id) VALUES (:order_id, :product_id)";
    $stmtItem = $pdo->prepare($sqlItem);

    // Loop through each product ID and insert it into the order_items table
    foreach ($productIds as $productId) {
        $stmtItem->bindParam(':order_id', $orderId);
        $stmtItem->bindParam(':product_id', $productId);
        $stmtItem->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Order placed successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error placing order: ' . $e->getMessage()]);
}
?>
