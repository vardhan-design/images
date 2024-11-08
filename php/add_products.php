<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);
// Read POST data
// Retrieve form data (text inputs)
$product_name = $data['product_name'] ?? null;
$brand = $_POST['brand'] ?? null;
$description = $data['description'] ?? null;
$fabriccare = $data['fabriccare'] ?? null;
$sizeoption = $data['sizeoption'] ?? null;
$coloroption = $data['coloroption'] ?? null;
$price = $data['price'] ?? null;
$stock = $data['stock'] ?? null;
$mfgdate = $data['mfgdate'] ?? null;
$category_name = $data['category_name'] ?? null;
$image_url = $data['image_url'] ?? null;
$image2 = $data['image2'] ?? null;
$image3 = $data['image3'] ?? null;
$image4 = $data['image4'] ?? null;

// Basic validation


// Insert category into categories table (if it doesn't already exist)
$category_query = "INSERT IGNORE INTO categories (category_name) VALUES (?)";
$stmt = $conn->prepare($category_query);
$stmt->bind_param("s", $category_name);
$stmt->execute();
$stmt->close();

// Get the category_id
$category_query = "SELECT category_id FROM categories WHERE category_name = ?";
$stmt = $conn->prepare($category_query);
$stmt->bind_param("s", $category_name);
$stmt->execute();
$result = $stmt->get_result();
$category_row = $result->fetch_assoc();
$category_id = $category_row['category_id'];
$stmt->close();

// Insert product into products table
$product_query = "INSERT INTO products (name, brand, description, price, category_id,fabric,size_options,color_options,image_url,image2,image3,image4) VALUES (?,  ?, ?, ?,?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("sssdisssssss", 
$product_name,      // string
$brand,             // string
$description,       // string
$price,             // double
$category_id,       // integer
$fabriccare,        // string
$sizeoption,        // string
$coloroption,       // string
$image_url,         // string
$image2,            // string
$image3,            // string
$image4);           // string
$stmt->execute();
$product_id = $stmt->insert_id; // Get the inserted product_id
$stmt->close();

// Insert quantity into product_inventory table
$inventory_query = "INSERT INTO product_inventory (product_id, quantity) VALUES (?, ?)";
$stmt = $conn->prepare($inventory_query);
$stmt->bind_param("ii", $product_id, $stock);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding product to inventory: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

