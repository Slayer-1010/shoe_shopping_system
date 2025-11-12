<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Check if username, email, or phone already exists
    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' OR phone='$phone' LIMIT 1";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        $existing_user = mysqli_fetch_assoc($result);
        if ($existing_user['username'] == $username) {
            $error = "Username already exists. Please choose a different one.";
        } elseif ($existing_user['email'] == $email) {
            $error = "Email already exists. Please use a different email.";
        } elseif ($existing_user['phone'] == $phone) {
            $error = "Phone number already exists. Please use a different phone number.";
        }
    } else {
        // Server-side validation for phone number
        if (!preg_match('/^98\d{8}$/', $phone)) {
            $error = "Phone number must be 10 digits long and start with 98.";
        } else {
            $query = "INSERT INTO users (username, email, password, phone, address) 
                      VALUES ('$username', '$email', '$password', '$phone', '$address')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
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
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            margin-bottom: 1.5rem;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 0.5rem;
            text-align: left;
            color: #555;
        }

        input[type="text"], input[type="email"], input[type="password"], textarea {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            padding: 0.75rem;
            background-color: #5cb85c;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .login-link {
            margin-top: 1rem;
        }

        .login-link a {
            color: #5cb85c;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
    <script>
        function validateForm() {
            const phone = document.getElementById('phone').value;
            const phonePattern = /^98\d{8}$/;
            if (!phonePattern.test(phone)) {
                alert("Phone number must be 10 digits long and start with 98.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST" onsubmit="return validateForm()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="4"></textarea>
            <button type="submit">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
