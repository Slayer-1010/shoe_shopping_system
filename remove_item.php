<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $query = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Item removed successfully.";
    } else {
        echo "Failed to remove item.";
    }
}

mysqli_close($conn);
?>
