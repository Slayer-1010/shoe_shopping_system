<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: auto;
            display: flex;
            justify-content: center;
        }

        .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #575757;
            color: white;
        }

        .navbar .right {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .about-header {
            text-align: center;
            padding: 20px 0;
        }

        .about-header h1 {
            font-size: 36px;
            color: #6c63ff;
        }

        .about-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            align-items: center;
        }

        .about-content img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .about-text {
            flex: 1;
            font-size: 18px;
            color: #666;
        }

        .section-header {
            text-align: center;
            margin: 40px 0 20px;
        }

        .section-header h2 {
            font-size: 28px;
            color: #6c63ff;
        }

        .brands-section, .categories-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .brand, .category {
            width: 200px;
            text-align: center;
        }

        .brand img, .category img {
            width: 150px;
            height: auto;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .brand img:hover, .category img:hover {
            transform: scale(1.1);
        }

        .brand h3, .category h3 {
            font-size: 20px;
            margin: 10px 0;
            color: #333;
        }

        .brand p, .category p {
            font-size: 16px;
            color: #666;
        }
        /* Footer styling */
.footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 10px;
    margin-top: 20px; /* Space above the footer */
}

.footer p {
    margin: 0;
    font-size: 14px;
}
    </style>
</head>
<body>
<div class="navbar">
    <a href="index.php">Home</a>
    <a href="about.php">About us</a>
    <div class="logo" style="flex-grow: 1; text-align: center; margin-left: -140px;">
    <img src="sign/logo.png" alt="Company Logo" style="height: 50px; width: 260px;">
    </div>
    <div class="right">
        <a href="login.php">Logout</a>
        <a href="cart.php"><img src="sign/cart.jpg" alt="Cart" style="width:35px; height:24px;"></a>    </div>
    </div>
</div>

<div class="container">
    <div class="about-header">
        <h1>About Us</h1>
    </div>
    <div class="about-content">
        <div class="about-text">
            <p>Welcome to our Shoe Store! We are dedicated to providing you with the best quality shoes that combine style and comfort. Our store was founded with the belief that everyone deserves to have access to high-quality footwear that meets their needs and preferences.</p>
            <p>Our journey began with a simple idea: to offer a wide range of shoes that cater to different tastes and lifestyles. We take pride in our diverse collection, which includes everything from casual sneakers to elegant dress shoes.</p>
        </div>
    </div>

    <div class="section-header">
    <h2>Brands Provided</h2>
</div>

    <div class="brands-section">
        <div class="brand">
            <img src="brands/nikelogo.jpg" alt="Nike">
            <h3>Nike</h3>
            <p>Innovative athletic footwear for all.</p>
        </div>
        <div class="brand">
            <img src="brands/pumalogo.jpg" alt="Puma">
            <h3>Puma</h3>
            <p>Performance and sport-inspired lifestyle products.</p>
        </div>
        <div class="brand">
            <img src="brands/adidaslogo.jpg" alt="Adidas">
            <h3>Adidas</h3>
            <p>Leading sports brand with cutting-edge designs.</p>
        </div>
    </div>

</div>
<footer class="footer">
    <p>&copy; 2024 All Rights Reserved</p>
</footer>
<script>
    document.getElementById('view-profile-btn').addEventListener('click', () => {
        document.getElementById('profile-modal').style.display = 'block';
    });

    document.getElementById('close-modal-btn').addEventListener('click', () => {
        document.getElementById('profile-modal').style.display = 'none';
    });
</script>
</body>
</html>
