<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "ecommerceweb"; // Ensure this database exists

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extract the JSON input
$data = json_decode(file_get_contents("php://input"), true);
$categoryNames = $data['categories'] ?? []; // Expecting an array of category names
$sizes = $data['sizes'] ?? [];              // Array of sizes
$colors = $data['colors'] ?? [];            // Array of colors
$maxPrice = $data['maxPrice'] ?? null;      // Maximum price

// Prepare response array
$response = [
    'filters' => [
        'categories' => $categoryNames,
        'sizes' => $sizes,
        'colors' => $colors,
        'maxPrice' => $maxPrice
    ],
    'products' => [],
    'count' => 0
];

// Fetch category IDs based on the provided category names
$categoryIds = [];
if (!empty($categoryNames)) {
    $escapedCategoryNames = array_map(function($name) use ($conn) {
        return "'" . $conn->real_escape_string($name) . "'";
    }, $categoryNames);

    $categoryNamesList = implode(",", $escapedCategoryNames);
    $categoryQuery = "SELECT category_id FROM categories WHERE category_name IN ($categoryNamesList)";
    $categoryResult = $conn->query($categoryQuery);

    if ($categoryResult && $categoryResult->num_rows > 0) {
        while ($row = $categoryResult->fetch_assoc()) {
            $categoryIds[] = $row['category_id'];
        }
    }
}

// Only fetch products if category IDs were found
if (!empty($categoryIds)) {
    $idsList = implode(",", $categoryIds);
    $productQuery = "SELECT * FROM products WHERE category_id IN ($idsList)";

   // Add size filter if sizes are provided
    if (!empty($sizes)) {
        $sizesList = implode("','", array_map([$conn, 'real_escape_string'], $sizes));
        $productQuery .= " AND size_guide IN ('$sizesList')";
    }
    

    // Add color filter if colors are provided
    if (!empty($colors)) {
        $colorsList = implode("','", array_map([$conn, 'real_escape_string'], $colors));
        $productQuery .= " AND color_options IN ('$colorsList')";
    }
/*
    // Add max price filter if provided
    if ($maxPrice !== null) {
        $maxPrice = $conn->real_escape_string($maxPrice);
        $productQuery .= " AND price <= $maxPrice";
    }*/

    $productResult = $conn->query($productQuery);
    $products = [];
    // Fetch products based on all filters
    if ($productResult && $productResult->num_rows > 0) {
        while ($row = $productResult->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode(['success' => true, 'products' => $products]);
    }else
    {
        echo json_encode(['success' => false, 'products' => $products]);
    }
} else {
    // No matching categories found
    file_put_contents("php://stderr", "No matching categories found.\n");
    
}



// Close connection
$conn->close();
?>
