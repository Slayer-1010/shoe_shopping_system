<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get product ID from URL parameter
$product_id = $_GET['id'];

// Query to retrieve product details
$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}

// Check if product exists
if (mysqli_num_rows($result) > 0) {
  $product = mysqli_fetch_assoc($result);
} else {
  header("Location: index.php"); // Redirect to index page if product not found
  exit;
}

// Retrieve user data from database for profile modal
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

?>

<html>
<head>
  <title>Product Detail</title>
  <link rel="stylesheet" href="styles.css">
  <style>
        /* Global Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f7f7f7;
            background-image: linear-gradient(to bottom, #f7f7f7, #fff);
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        p {
            margin-bottom: 30px;
            color: #666;
            font-size: 18px;
            line-height: 1.5;
        }

        /* Product Card Styles */
        .product-card {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .product-image {
            width: 40%;
            height: 200px;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }

        .product-info {
            width: 60%;
            padding-left: 20px;
        }

        .product-options {
            margin-bottom: 20px;
        }

        .product-options label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
            font-size: 18px;
        }

        .product-options select {
            width: 100%;
            height: 40px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 18px;
            font-family: 'Open Sans', sans-serif;
        }

        .product-options select:focus {
            border-color: #337ab7;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .product-options input[type="number"] {
            width: 100%;
            height: 40px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 18px;
            font-family: 'Open Sans', sans-serif;
        }

        .product-options input[type="number"]:focus {
            border-color: #337ab7;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .product-actions {
            margin-top: 20px;
        }

        .product-actions button {
            width: 100%;
            height: 40px;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-family: 'Open Sans', sans-serif;
            cursor: pointer;
        }

        .btn-add-to-cart {
            background-color: #337ab7;
            color: #fff;
        }

        .btn-add-to-cart:hover {
            background-color: #23527c;
        }

        /* Out of Stock Styling */
.out-of-stock {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    margin-top: 20px;
}

        /* Navbar Styles */
        .navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center; /* Center the navbar items */
        }

        .navbar a {
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

        /* Profile Modal Styles */
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 3;
        }

        .modal-content {
            background-color: #fff;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .modal-header h1 {
            font-size: 24px;
            margin: 0;
        }

        .close-btn {
            font-size: 24px;
            cursor: pointer;
        }

        .profile-pic {
            text-align: center;
            margin: 20px;
        }

        .profile-pic img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info {
            padding: 20px;
        }

        .profile-info p {
            margin: 10px 0;
        }

        .edit-btn {
            display: block;
            background-color: #6c63ff;
            color: #fff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #5548c8;
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .product {
                flex: 1 1 calc(50% - 20px); /* 2 products per row */
            }
        }

        @media (max-width: 768px) {
            .product {
                flex: 1 1 calc(100% - 20px); /* 1 product per row */
            }

            .navbar a {
                flex: 1 1 100%;
            }
        }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <a href="index.php">Home</a>
  <a href="about.php">About us</a>
  <a href="#" id="view-profile-btn">View Profile</a>
  <div class="logo" style="flex-grow: 1; text-align: center; margin-right: 140px;">
    <img src="sign/logo.png" alt="Company Logo" style="height: 50px; width: 260px;">
    </div>
  <a href="cart.php" style="margin-left: auto;"><img src="sign/cart.jpg" alt="Cart" style="width:35px; height:24px;"></a>
</div>

<!-- Profile Modal -->
<div id="profile-modal" class="modal">
    <div class="modal-content">
        <?php
        // Retrieve user data from database
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE user_id='$user_id'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        ?>
        <div class="modal-header">
            <h1>Profile</h1>
            <button class="close-btn" id="close-modal-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="profile-pic">
                <img src="profile_pic/<?php echo htmlspecialchars($user['profile-pic']); ?>" alt="Profile Picture">
            </div>
            <div class="profile-info">
                <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
                <p>Address: <?php echo htmlspecialchars($user['address']); ?></p>
            </div>
            <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
        </div>
    </div>
</div>

<!-- Product Detail Page -->
<div class="container">
  <div class="product-card">
    <div class="product-image">
      <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-info">
      <h1><?php echo htmlspecialchars($product['name']); ?></h1>
      <p><?php echo htmlspecialchars($product['description']); ?></p>
      <p><strong>Price: </strong>Rs.<?php echo htmlspecialchars($product['price']); ?></p>
      
      <?php if ($product['stock'] > 0): ?>
      <div class="product-actions">
        <form action="add_to_cart.php" method="get">
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
          <div class="product-options">
            <label for="size">Size:</label>
            <select id="size" name="size">
              <option value="36">36</option>
              <option value="37">37</option>
              <option value="38">38</option>
              <option value="39">39</option>
              <option value="40">40</option>
              <option value="41">41</option>
              <option value="42">42</option>
              <option value="43">43</option>
              <option value="44">44</option>
            </select>
          </div>
          <div class="product-options">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>">
          </div>
          <button class="btn-add-to-cart" type="submit">Add to Cart</button>
        </form>
      </div>
      <?php else: ?>
      <div class="out-of-stock">
        <p>Out of Stock<br>visit later</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  // Profile modal script
  var modal = document.getElementById("profile-modal");
  var btn = document.getElementById("view-profile-btn");
  var closeBtn = document.getElementById("close-modal-btn");

  btn.onclick = function() {
    modal.style.display = "flex";
  }

  closeBtn.onclick = function() {
    modal.style.display = "none";
  }

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>

</body>
</html>
