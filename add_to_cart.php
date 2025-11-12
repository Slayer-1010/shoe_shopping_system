<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_GET['id']);
    $quantity = $_GET['quantity']; // Get the quantity from the form
    $size = $_GET['size']; // Get the size from the form

    // Check if the product is already in the cart
    $checkQuery = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // If product is already in the cart, update the quantity
        $updateQuery = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'";
        mysqli_query($conn, $updateQuery);
    } else {
        // If product is not in the cart, insert it
        $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, size) VALUES ($user_id, $product_id, $quantity, '$size')";
        mysqli_query($conn, $insertQuery);
    }

    header("Location: cart.php");
    exit();
}
?>