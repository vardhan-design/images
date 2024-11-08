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
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);
$input = strtolower(trim($data['username'] ?? '')); // Get input (email/mobile number)
$password = trim($data['password'] ?? ''); 

// Determine if the input is an email or mobile number
$emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
$isEmail = preg_match($emailPattern, $input);
$isMobile = preg_match('/^\d{10}$/', $input); // Assuming mobile numbers are 10 digits

// Prepare the query based on the input type
if ($isEmail) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
} elseif ($isMobile) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE contact = ?");
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input format.']);
    exit();
}

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

// Bind parameters and execute the query
$stmt->bind_param("s", $input);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Debugging: output user data
    // error_log(print_r($user, true)); // Uncomment for debugging, but remove in production
    

    // Verify the password
    if (password_verify($password, $user['password'])) {
        // User authenticated successfully
        echo json_encode([
            'success' => true,
            'user_id' => $user['user_id'], // User ID
            'email' => $user['email'], // User's email
            'contact' =>$user['contact'],
        ]);
    } else {
        // Invalid password
        echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
        error_log("Password verification failed for user: " . $input);
    }
} else {
    // No user found
    echo json_encode(['success' => false, 'message' => 'Incorrect username .']);
    error_log("No user found for input: " . $input);
}

// Clean up
$stmt->close(); 
$conn->close(); 
?>
