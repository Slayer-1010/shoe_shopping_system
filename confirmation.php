<?php
session_start();
require_once 'db.php'; // Database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$invoice = isset($_GET['invoice']) ? urldecode($_GET['invoice']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            text-align: center;
        }

        .back-btn, .invoice-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #6c63ff;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover, .invoice-btn:hover {
            background-color: #5548c8;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="contact.php">Contact</a>
    <a href="about.php">About</a>
</div>

<div class="container">
    <h1>Order Confirmation</h1>
    <p>Thank you for your order! Your order has been placed successfully.</p>
    <?php if ($invoice): ?>
        <a href="<?php echo htmlspecialchars($invoice); ?>" class="invoice-btn">Download your invoice</a>
    <?php endif; ?>
    <a href="index.php" class="back-btn">Continue Shopping</a>
</div>

</body>
</html>
