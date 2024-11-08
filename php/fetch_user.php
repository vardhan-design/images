<?php
header('Content-Type: application/json');

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
$userId = $_GET['id'] ?? '';
if (empty($userId)) {
    echo json_encode(['error' => 'User ID is required']);
    exit();
}

// Prepare and execute the query to fetch user data
$stmt = $conn->prepare("SELECT user_id, email,image,address,contact, name FROM users WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $userId); // 's' for string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>
