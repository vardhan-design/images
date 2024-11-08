<?php
// login.php

header('Content-Type: application/json');
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($data['username'] ?? '')); // Normalize email
$password = $data['password'] ?? ''; 
$productId = $data['selectedProductId'] ?? ''; // Get the selected product ID

// Prepare and execute the query to check user credentials
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Debugging: output user data
    error_log(print_r($user, true));

    if (password_verify($password, $user['password'])) {
        // User authenticated successfully

        // Update the user's product ID in the users table
        if (!empty($productId)) {
            $updateStmt = $conn->prepare("UPDATE users SET product_id = ? WHERE user_id = ?");
            if ($updateStmt) {
                $updateStmt->bind_param("ss", $productId, $email);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare update statement: ' . $conn->error]);
                exit();
            }
        }

        echo json_encode([
	 'success' => true,
            'user_id' => $user['user_id'], // Add user ID
            'email' => $user['email'], // Add email
	]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect username or password.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect username or password.']);
}

$stmt->close(); 
$conn->close(); 
?>
