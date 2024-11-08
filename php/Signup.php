<?php
// signup.php

// Database connection
$servername = "localhost";
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "ecommerceweb"; // Ensure this database exists

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . htmlspecialchars($conn->connect_error)]);
    exit;
}

// Read JSON input data
$data = json_decode(file_get_contents('php://input'), true);

// Get the values from the POST request
$mobilenumber = $data['mobilenumber'] ?? null;
$name = $data['name'] ?? null;
$passwordInput = trim($data['password'] ?? '');
$email = $data['email'] ?? null;


// Hash the password before storing it
$hashed_password = password_hash($passwordInput, PASSWORD_DEFAULT);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO users (contact, name, password, email) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Error preparing the statement: ' . htmlspecialchars($conn->error)]);
    exit;
}

// Bind the parameters
if (!$stmt->bind_param("ssss", $mobilenumber, $name, $hashed_password, $email)) {
    echo json_encode(['success' => false, 'message' => 'Error binding parameters: ' . htmlspecialchars($stmt->error)]);
    exit;
}

// Execute the statement
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error executing the statement: ' . htmlspecialchars($stmt->error)]);
} else {
    echo json_encode(['success' => true, 'message' => 'User created successfully.']);
}

// Close the statement and the connection
$stmt->close();
$conn->close();
?>
