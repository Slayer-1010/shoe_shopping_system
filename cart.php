<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$subtotal = 0;
$shipping = 0;
$total = 0;

$query = "SELECT cart.*, products.name, products.price FROM cart 
          JOIN products ON cart.product_id = products.product_id 
          WHERE cart.user_id = $user_id";
$result = mysqli_query($conn, $query);
$cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$total = $subtotal + $shipping;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table th, .cart-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .cart-table th {
            background-color: #f4f4f4;
        }

        .totals {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        .totals p {
            margin: 5px 0;
        }

        .checkout-btn {
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

        .checkout-btn:hover {
            background-color: #5548c8;
        }

        .quantity {
            display: flex;
            align-items: center;
        }

        .plus, .minus {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
        }

        .plus:hover, .minus:hover {
            background-color: #3e8e41;
        }

        .plus:active, .minus:active {
            background-color: #3e8e41;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .quantity input[type="number"] {
            width: 40px;
            height: 30px;
            text-align: center;
            font-size: 18px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }

        .remove {
            background-color: #FF3737;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .remove:hover {
            background-color: #FF6969;
        }

        .remove:active {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .remove:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .checkout-btn.disabled {
    background-color: #ccc;
    cursor: not-allowed;
    pointer-events: none;
}

    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php">Home</a>
    <a href="about.php">About us</a>
    <div class="logo" style="flex-grow: 1; text-align: center; margin-right: 140px;">
        <img src="sign/logo.png" alt="Company Logo" style="height: 50px; width: 260px;">
    </div>
</div>

<div class="container">
    <h1>Shopping Cart</h1>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($cartItems): ?>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>Rs.<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <form>
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <div class="quantity">
                                <button class="minus" data-cart-id="<?php echo $item['cart_id']; ?>">-</button>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                                <button class="plus" data-cart-id="<?php echo $item['cart_id']; ?>">+</button>
                            </div>
                        </form>
                    </td>
                    <td>
                        <form>
                            <button class="remove" data-cart-id="<?php echo $item['cart_id']; ?>">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Your cart is empty.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="totals">
        <p>Subtotal: Rs.<?php echo number_format($subtotal, 2); ?></p>
        <p>Shipping: Free</p>
        <p>Total: Rs.<?php echo number_format($total, 2); ?></p>
    </div>
    <?php if ($cartItems): ?>
    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
<?php else: ?>
    <a href="#" class="checkout-btn disabled">Proceed to Checkout</a>
<?php endif; ?>

    <a href="index.php" id="go-home" class="checkout-btn" onclick="saveScrollPosition()">Continue Shopping</a>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const plusButtons = document.querySelectorAll(".plus");
    const minusButtons = document.querySelectorAll(".minus");

    plusButtons.forEach(button => {
        button.addEventListener("click", function() {
            const cartId = this.getAttribute("data-cart-id");
            const quantityInput = this.parentNode.querySelector("input[name='quantity']");
            const currentQuantity = parseInt(quantityInput.value);
            const newQuantity = currentQuantity + 1;

            updateQuantity(cartId, newQuantity, function(response) {
                if (response === 'Exceeds stock') {
                    alert('Quantity exceeds available stock.');
                } else {
                    quantityInput.value = newQuantity;
                    refreshCart();
                }
            });
        });
    });

    minusButtons.forEach(button => {
        button.addEventListener("click", function() {
            const cartId = this.getAttribute("data-cart-id");
            const quantityInput = this.parentNode.querySelector("input[name='quantity']");
            const currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity - 1;

            if (newQuantity < 1) {
                newQuantity = 1;
            }

            updateQuantity(cartId, newQuantity, function(response) {
                if (response !== 'Exceeds stock') {
                    quantityInput.value = newQuantity;
                    refreshCart();
                }
            });
        });
    });

    const removeButtons = document.querySelectorAll(".remove");

    removeButtons.forEach(button => {
        button.addEventListener("click", function() {
            const cartId = this.getAttribute("data-cart-id");
            removeItem(cartId, function() {
                refreshCart();
            });
        });
    });
});

function updateQuantity(cartId, newQuantity, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            callback(xhr.responseText);
        }
    };

    xhr.send("cart_id=" + cartId + "&quantity=" + newQuantity);
}

function removeItem(cartId, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "remove_item.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            callback();
        }
    };

    xhr.send("cart_id=" + cartId);
}

function refreshCart() {
    window.location.reload();
}

</script>

</body>
</html>
