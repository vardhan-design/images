<?php
// get_stats.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerceweb";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch total products
    $stmt = $pdo->query("SELECT COUNT(*) as totalProducts FROM products");
    $totalProducts = $stmt->fetchColumn();

    // Fetch total users
    $stmt = $pdo->query("SELECT COUNT(*) as totalUsers FROM users");
    $totalUsers = $stmt->fetchColumn();

    // Fetch total orders
    $stmt = $pdo->query("SELECT COUNT(*) as totalOrders FROM orders");
    $totalOrders = $stmt->fetchColumn();

    echo json_encode([
        'totalProducts' => $totalProducts,
        'totalUsers' => $totalUsers,
        'totalOrders' => $totalOrders
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
