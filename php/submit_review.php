<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the required data is present
if (isset($data['product_id'], $data['rating'], $data['review_text'])) {
    $product_id = $data['product_id'];
    $rating = $data['rating'];
    $review_text = $data['review_text'];

    // Prepare and execute SQL to insert the review into the reviewtable
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, rating, review_text) VALUES (?, ?, ?)");
    
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $stmt->bind_param('iis', $product_id, $rating, $review_text); // 'i' for int, 's' for string
    $stmt->execute();
    
    // Check for successful insertion
    if ($stmt->affected_rows > 0) {
        // Get updated reviews and average rating
        $stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
        
        // Calculate average rating
        $totalRating = 0;
        $reviewCount = count($reviews);
        
        if ($reviewCount > 0) {
            foreach ($reviews as $review) {
                $totalRating += $review['rating'];
            }
            $averageRating = $totalRating / $reviewCount;
        } else {
            $averageRating = 0; // No reviews, set average to 0
        }

        // Return success response with updated reviews
        echo json_encode([
            'success' => true,
            'updatedReviews' => $reviews,
            'averageRating' => $averageRating
        ]);
    } else {
        // Error during insert
        echo json_encode([
            'success' => false,
            'message' => 'Failed to submit review.'
        ]);
    }
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Invalid input
    echo json_encode([
        'success' => false,
        'message' => 'Required data missing.'
    ]);
}
?>
