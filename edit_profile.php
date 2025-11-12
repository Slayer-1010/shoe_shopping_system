<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Retrieve user data from the database
$query = "SELECT * FROM users WHERE user_id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Initialize error and success messages
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_pic = $_FILES['profile_pic'];

    // Validate input
    if (empty($username) || empty($email) || empty($phone) || empty($address)) {
        $error_message = "Please fill in all fields.";
      } else {
        // Update user data in the database
        $query = "UPDATE users SET username='$username', email='$email', phone='$phone', address='$address' WHERE user_id='$user_id'";
        if (!mysqli_query($conn, $query)) {
            $error_message = "Error updating profile: " . mysqli_error($conn);
        } else {
            // Handle profile picture upload
            if (!empty($profile_pic['name'])) {
                $file_name = $_FILES['profile_pic']['name'];
                $tempname = $_FILES['profile_pic']['tmp_name'];
                $folder = 'profile_pic/' . $file_name;
    
                // Check if the directory exists, if not, create it
                if (!is_dir('profile_pic')) {
                    mkdir('profile_pic', 0777, true);
                }
    
                // Update profile picture in the database
                $query = "UPDATE users SET `profile-pic`='$file_name' WHERE user_id='$user_id'";
                if (mysqli_query($conn, $query) && move_uploaded_file($tempname, $folder)) {
                    // Redirect to index.php upon successful update
                    header("Location: index.php");
                    exit();
                } else {
                    $error_message .= " Error uploading profile picture.";
                }
            } else {
                // Redirect to index.php upon successful update if no profile picture was uploaded
                header("Location: index.php");
                exit();
            }
        }
    }
    
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        /* Global Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-top: 0;
            color: #333;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        label {
            margin-bottom: 10px;
            color: #666;
        }

        input[type="text"], input[type="email"], input[type="tel"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #3e8e41;
        }

        .error {
            color: #ff0000;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .success {
            color: #00ff00;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if ($error_message): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            <label for="address">Address:</label>
            <textarea id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
            <label for="profile_pic">Profile Picture:</label>
            <input type="file" id="profile_pic" name="profile_pic">
            <input type="submit" value="Save Changes">
            <div>
              <img src="profile_pic/<?php echo htmlspecialchars($user['profile-pic']); ?>" alt="Profile Picture"/>
            </div>
        </form>
    </div>
</body>
</html>
