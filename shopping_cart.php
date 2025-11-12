<?php
session_start();
require_once 'db.php'; // Database connection file

// Initialize cart if not already done
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get product details from POST request
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch product details from database
    $query = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $query);

    // Check if product exists
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        // Prepare item array
        $item = [
            'product_id' => $product['product_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];

        // Add item to cart
        $_SESSION['cart'][] = $item;
    }
}

// Fetch cart items from session
$cartItems = $_SESSION['cart'];

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Display cart items
foreach ($cartItems as $item) {
    echo '<div>';
    echo '<h3>' . $item['name'] . '</h3>';
    echo '<p>Quantity: ' . $item['quantity'] . '</p>';
    echo '<p>Price: $' . ($item['price'] * $item['quantity']) . '</p>';
    echo '</div>';
}

echo '<form action="checkout.php" method="post">';
echo '<button type="submit">Proceed to Checkout</button>';
echo '</form>';

?>
