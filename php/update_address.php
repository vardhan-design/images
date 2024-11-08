<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Extract the user ID and new address
$userId = $data['id'];
$name = $data['name'];
$email = $data['email'];
$address = $data['adress']; // Corrected spelling from $adress to $address
$contact = $data['contact'];
$image = $data['image']; 


// Prepare and execute the SQL statement to update the user's address
$sql = "UPDATE users SET name = ?, email = ?, address = ?, contact = ?, image=? WHERE user_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die(json_encode(['success' => false, 'error' => 'SQL prepare failed: ' . $conn->error]));
}

$stmt->bind_param("sssssi", $name, $email, $address, $contact, $image, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'address' => $address, 'name' => $name, 'email' => $email, 'contact' => $contact, 'image' => $image]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update address']);
}

$stmt->close();
$conn->close();
?>
