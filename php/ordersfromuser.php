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

// Read the input (assuming the user ID is passed in JSON format)
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'] ?? ''; // Get user ID from input

if (empty($userId)) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

// SQL query to fetch user details, ordered quantities, product information, inventory quantity, and reviews for the specific user
$sqlUserOrders = "
    SELECT 
        u.user_id,
        u.image,
        u.name,
        u.email,
        u.contact,
        u.address,
        COALESCE(SUM(oi.quantity), 0) AS ordered_quantity,
        p.product_id,
        p.name AS product_name,
        p.price AS product_price,
        p.image_url,
        p.image2,
        i.quantity AS inventory_quantity,
        r.review_text AS review_text,
        r.rating AS review_rating
    FROM 
        users u
    LEFT JOIN 
        orders o ON u.user_id = o.user_id
    LEFT JOIN 
        order_items oi ON o.order_id = oi.order_id
    LEFT JOIN 
        products p ON oi.product_id = p.product_id
    LEFT JOIN 
        product_inventory i ON p.product_id = i.product_id  -- Join with inventory table
    LEFT JOIN 
        reviews r ON p.product_id = r.product_id  -- Join with reviews table
    WHERE 
        u.user_id = ?
    GROUP BY 
        u.user_id, p.product_id, r.review_text, r.rating
    ORDER BY 
        u.user_id
";

$stmtUserOrders = $conn->prepare($sqlUserOrders);
if (!$stmtUserOrders) {
    die("Preparation failed: (" . $conn->errno . ") " . $conn->error);
}

$stmtUserOrders->bind_param("i", $userId);

if (!$stmtUserOrders->execute()) {
    die("Execution failed: (" . $stmtUserOrders->errno . ") " . $stmtUserOrders->error);
}

$userOrderResult = $stmtUserOrders->get_result();

// Initialize arrays for user details and products
$userDetails = [];
$products = [];

// Fetch user order details and structure the data
while ($row = $userOrderResult->fetch_assoc()) {
    // Populate user details only once
    if (empty($userDetails)) {
        $userDetails[0] = [
            'user_id' => $row['user_id'],
            'image' => $row['image'],
            'name' => $row['name'],
            'email' => $row['email'],
            'contact' => $row['contact'],
            'address' => $row['address'],
            'ordered_quantity' => $row['ordered_quantity']
        ];
    }

    // Structure each product with relevant details and reviews
    $productId = $row['product_id'];
    if (!isset($products[$productId])) {
        $products[$productId] = [
            'product_id' => $productId,
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'image_url'=> $row['image_url'],
            'image2'=> $row['image2'],
            'quantity' => $row['inventory_quantity'],
            'reviews' => []
        ];
    }

    // Add review to the product if available
    if (!empty($row['review_text']) && !empty($row['review_rating'])) {
        $products[$productId]['reviews'][] = [
            'text' => $row['review_text'],
            'rating' => $row['review_rating']
        ];
    }
}

// Convert products associative array to indexed array
$products = array_values($products);

// Check if there are any ordered products for the user
if (empty($products)) {
    echo json_encode(['success' => true, 'message' => 'No orders found for this user']);
    exit;
}

// Return response as JSON with user details and products in separate arrays
echo json_encode(['success' => true, 'user' => $userDetails, 'products' => $products]);

// Clean up
$stmtUserOrders->close();
$conn->close();
?>
