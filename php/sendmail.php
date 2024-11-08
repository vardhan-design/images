<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create an instance of PHPMailer
$mail = new PHPMailer(true);
// Read the JSON input
$data = json_decode(file_get_contents("php://input"), true);

$products = $data['products'];
$subtotal = $data['subtotal'];
$shipping = $data['shipping'];
$total = $data['total'];

try {
    // SMTP configuration
    // SMTP server configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'vthemonkey.d.luffy@gmail.com'; // Your Gmail address
    $mail->Password = 'xidp fezc skms vawu'; // App password from Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Set email sender and recipient
    $mail->setFrom('vthemonkey.d.luffy@gmail.com', 'Sender Name');
    $mail->addAddress('johnsnowtagerian@gmail.com', 'Recipient Name'); // Recipient’s email address

    // Attach the image as an embedded image
    

    // Set email format to HTML
    $mail->isHTML(true);
    $mail->Subject = 'Order Confirmation';
    $htmlContent = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: Arial, sans-serif; }
            .product-summary { width: 100%; border: 1px solid #ccc; padding: 10px; border-radius: 4px; background-color: #f9f9f9; margin-top: 20px; }
            .product-summary h2 { font-size: 20px; }
            .product-item { display: flex; justify-content: space-between; margin: 10px 0; }
            .product-images img { height: 100px; width: auto; object-fit: contain; }
            .price { color: #e63946; font-weight: bold; }
            .total-section { display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='product-summary'>
            <h2>Order Summary</h2>";
    
            // Add each product item to the HTML content
            foreach ($data['products'] as $product) {
                $product['image'] = str_replace("http://localhost/E-commerceebPage/", "https://raw.githubusercontent.com/vardhan-design/images/master/", $product['image']);

                $htmlContent .= "
                <div class='product-item'>
                    <div class='product-images'>
                        <img src='{$product['image']}' alt='{$product['name']}'>
                    </div>
                    <div>
                        <div class='product-name'>{$product['name']}</div>
                        <div class='price'>{$product['price']}</div>
                    </div>
                </div>";
            }
    
            // Add subtotal, shipping, and total
            $htmlContent .= "
            <div class='product-item'>
                <span>Subtotal</span>
                <span>{$data['subtotal']}</span>
            </div>
            <div class='product-item'>
                <span>Shipping</span>
                <span>{$data['shipping']}</span>
            </div>
            <div class='total-section'>
                <span>Total</span>
                <span>{$data['total']}</span>
            </div>
        </div>
    </body>
    </html>";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: yourname@example.com' . "\r\n";
    

    $mail->Body =  $htmlContent;

    // Send the email
    if ($mail->send()) {
        echo "Message has been sent.";
    } else {
        echo "Message could not be sent.";
    }

} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
