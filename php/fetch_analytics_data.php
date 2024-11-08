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

// SQL query to fetch products and their total ordered quantities
$sql = "
    SELECT 
        p.product_id,
        p.name AS product_name,
        SUM(oi.quantity) AS total_ordered_quantity
    FROM 
        products p
    LEFT JOIN 
        order_items oi ON p.product_id = oi.product_id
    LEFT JOIN 
        orders o ON oi.order_id = o.order_id
    GROUP BY 
        p.product_id, p.name
    ORDER BY 
        total_ordered_quantity DESC
";

$result = $conn->query($sql);

$analyticsData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $analyticsData[] = [
            'product_name' => $row['product_name'],
            'total_ordered_quantity' => (int)$row['total_ordered_quantity']
        ];
    }
}

// Return response as JSON
echo json_encode(['success' => true, 'data' => $analyticsData]);

// Clean up
$conn->close();
?>
