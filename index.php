<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoe Store</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
      /* Reset some basic styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: #f9f9f9;
    color: #333;
    line-height: 1.6;
}

/* Navbar styling */
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

/* Slideshow container styling */
.slideshow-container {
    position: relative;
    max-width: 100%;
    margin: auto;
    overflow: hidden;
    height: 100vh;
    border-radius: 0%; /* More rounded corners for a smoother look */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* Larger shadow for a more pronounced effect */
}

/* Slideshow images styling */
.slideshow-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
    transition: opacity 1s ease-in-out, transform 1s ease-in-out; /* Smooth fade and zoom transition */
}

/* Active image styling */
.active {
    display: ;
    opacity: 1;
    transform: scale(1.05); /* Slight zoom effect for the active image */
}
/* Product section styling */
.products-container {
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    width: 95%;
    max-width: 1200px;
}

.category-container {
    position: relative;
    margin-top: 40px;
}

.category {
    width: 100%;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #6c63ff;
    text-transform: uppercase;
}

.products-wrapper {
    display: flex;
    overflow: hidden;
    position: relative;
    padding: 0 50px;
}

.products {
    display: flex;
    gap: 20px;
    white-space: nowrap;
    transition: transform 0.3s ease;
}

.product {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    width: 250px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    flex: 0 0 auto;
}

.product:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.product img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.product h3 {
    font-size: 20px;
    margin: 10px 0;
    color: #333;
}

.product p {
    font-size: 18px;
    color: #666;
}

.product a {
    display: inline-block;
    background-color: #6c63ff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 10px;
    transition: background-color 0.3s ease;
}

.product a:hover {
    background-color: #5548c8;
}

/* Arrow buttons styling */
.arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    z-index: 10;
    border-radius: 50%;
    transition: background-color 0.3s;
}

.arrow:hover {
    background-color: rgba(0, 0, 0, 0.8);
}

.arrow.left {
    left: 0;
}

.arrow.right {
    right: 0;
}

/* Profile Modal styling */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
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
    position: relative;
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
    color: #333;
    transition: color 0.3s;
}

.close-btn:hover {
    color: #6c63ff;
}

/* Profile Info styling */
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
    font-size: 18px;
    color: #666;
}

.edit-btn {
    display: block;
    background-color: #6c63ff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    margin-top: 10px;
    transition: background-color 0.3s ease;
    text-align: center;
}

.edit-btn:hover {
    background-color: #5548c8;
}

.navbar .right {
    margin-left: auto;
    display: flex;
    gap: 10px;
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
<!-- Add a modal container to display the profile information -->
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

<div class="navbar">
    <a href="#">Home</a>
    <a href="about.php">About us</a>
    <a href="#" id="view-profile-btn">View Profile</a>
    <div class="logo" style="flex-grow: 1; text-align: center; margin-right: 140px;">
    <img src="sign/logo.png" alt="Company Logo" style="height: 50px; width: 260px;">
    </div>

    <div class="right">
        <a href="login.php">Logout</a>
        <a href="cart.php"><img src="sign/cart.jpg" alt="Cart" style="width:35px; height:24px;"></a>    </div>
</div>

<div class="slideshow-container">
    <?php
    $slideshow_dir = 'slideshow/';
    $images = glob($slideshow_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    foreach ($images as $image) {
        echo '<img src="' . htmlspecialchars($image) . '" alt="Slideshow Image">';
    }
    ?>
</div>

<div class="products-container">
    <?php
    require_once 'db.php'; // Database connection file

    // Fetch products from database joined with categories table
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              ORDER BY c.name, p.name";
    $result = mysqli_query($conn, $query);

    $current_category = '';
    while ($row = mysqli_fetch_assoc($result)) {
        if ($current_category !== $row['category_name']) {
            if ($current_category !== '') {
                echo '</div>'; // Close the previous products div
                echo '</div>'; // Close the previous products-wrapper div
                echo '<button class="arrow right" onclick="scrollRight(event)">&#8250;</button>';
                echo '</div>'; // Close the previous category-container div
            }
            $current_category = $row['category_name'];
            echo '<div class="category-container">';
            echo '<div class="category">' . htmlspecialchars($current_category) . '</div>';
            echo '<button class="arrow left" onclick="scrollLeft(event)">&#8249;</button>';
            echo '<div class="products-wrapper">';
            echo '<div class="products">';
        }
        echo '<div class="product">';
        echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p>Rs.' . htmlspecialchars($row['price']) . '</p>';
        echo '<a href="product_detail.php?id=' . htmlspecialchars($row['product_id']) . '">View Product</a>';
        echo '</div>';
    }
    echo '</div>'; // Close the last products div
    echo '</div>'; // Close the last products-wrapper div
    echo '<button class="arrow right" onclick="scrollRight(event)">&#8250;</button>';
    echo '</div>'; // Close the last category-container div
    ?>
</div>
<footer class="footer">
    <p>&copy; 2024 All Rights Reserved</p>
</footer>
<script>
    // Slideshow functionality
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let slides = document.querySelectorAll('.slideshow-container img');
        slides.forEach((slide, index) => {
            slide.style.display = 'none';
            if (index === slideIndex) {
                slide.style.display = 'block';
            }
        });
        slideIndex++;
        if (slideIndex >= slides.length) {
            slideIndex = 0;
        }
        setTimeout(showSlides, 2000); // Change image every 3 seconds
    }

    let productsWrappers = document.querySelectorAll('.products-wrapper');

    productsWrappers.forEach((productsWrapper, index) => {
        let scrollPosition = 0;
        let scrollWidth = productsWrapper.scrollWidth;
        let clientWidth = productsWrapper.clientWidth;
        let scrollKey = `scrollPosition_${index}`; // Unique key for each wrapper

        // Restore scroll position on page load
        if (localStorage.getItem(scrollKey)) {
            scrollPosition = localStorage.getItem(scrollKey);
            productsWrapper.scrollLeft = scrollPosition;
        }

        productsWrapper.addEventListener('scroll', () => {
            scrollPosition = productsWrapper.scrollLeft;
            localStorage.setItem(scrollKey, scrollPosition);
        });

        productsWrapper.nextElementSibling.addEventListener('click', () => {
            if (scrollPosition < scrollWidth - clientWidth) {
                productsWrapper.scrollBy({ left: 250, behavior: 'smooth' });
            }
        });

        productsWrapper.previousElementSibling.addEventListener('click', () => {
            if (scrollPosition > 0) {
                productsWrapper.scrollBy({ left: -250, behavior: 'smooth' });
            }
        });
    });

    document.getElementById('view-profile-btn').addEventListener('click', () => {
        document.getElementById('profile-modal').style.display = 'block';
    });

    document.getElementById('close-modal-btn').addEventListener('click', () => {
        document.getElementById('profile-modal').style.display = 'none';
    });

    // Save scroll position of the entire page before unloading
    window.addEventListener('beforeunload', () => {
        localStorage.setItem('pageScrollPosition', window.scrollY);
    });

    // Restore scroll position of the entire page on load
    window.addEventListener('load', () => {
        if (localStorage.getItem('pageScrollPosition')) {
            window.scrollTo(0, localStorage.getItem('pageScrollPosition'));
        }
    });
</script>


</body>
</html>
