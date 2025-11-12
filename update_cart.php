<?php
session_start();
require_once 'db.php';

if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo 'Invalid request';
    exit();
}

$cart_id = intval($_POST['cart_id']);
$new_quantity = intval($_POST['quantity']);

// Fetch the product_id and current stock from the database
$query = "SELECT product_id FROM cart WHERE cart_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $cart_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $product_id);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$query = "SELECT stock FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $stock);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Check if the desired quantity exceeds available stock
if ($new_quantity > $stock) {
    echo 'Exceeds stock';
    exit();
}

// Update the cart with the new quantity
$updateQuery = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
$stmt = mysqli_prepare($conn, $updateQuery);
mysqli_stmt_bind_param($stmt, 'ii', $new_quantity, $cart_id);
if (mysqli_stmt_execute($stmt)) {
    echo 'Quantity updated';
} else {
    echo 'Failed to update quantity';
}

mysqli_stmt_close($stmt);
?>
