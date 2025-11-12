<?php
session_start();
require_once 'db.php';

// Function to fetch the primary key field for a given table
function fetchPrimaryKeyField($conn, $table) {
    $query = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        return '';
    }
    $row = mysqli_fetch_assoc($result);
    return $row['Column_name'];
}

// Function to fetch data from the given table
function fetchData($conn, $table) {
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        echo "Error: " . mysqli_error($conn);
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// Ensure photo directory exists
$uploadDir = 'photo';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
// Define actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$table = isset($_GET['table']) ? $_GET['table'] : '';

// Initialize data arrays
$users = [];
$products = [];
$categories = [];
$orders = [];
$cart = [];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($table) {
        case 'users':
            // Handle add/edit users
            if (isset($_POST['username'])) {
                if ($action == 'edit' && isset($_POST['user_id'])) {
                    $query = "UPDATE users SET username = ?, password = ? WHERE user_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 'ssi', $_POST['username'], $_POST['password'], $_POST['user_id']);
                } else {
                    $query = "INSERT INTO users (username, password) VALUES (?, ?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 'ss', $_POST['username'], $_POST['password']);
                }
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Error: " . mysqli_error($conn);
                } else {
                    // Redirect to the users table view
                    header("Location: admin.php?table=users");
                    exit();
                }
                mysqli_stmt_close($stmt);
            }
            break;
            case 'products':
                // Handle add/edit products
                if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['category_id']) && isset($_POST['stock'])) {
                    $imagePath = '';
                    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                        // New image uploaded
                        $imagePath = $uploadDir . '/' . basename($_FILES['image']['name']);
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                            echo "Error: Failed to upload image.";
                            exit();
                        }
                    } else {
                        // No new image, use existing image
                        $imagePath = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
                    }
                    if ($action == 'edit' && isset($_POST['product_id'])) {
                        $query = "UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category_id = ?, stock = ? WHERE product_id = ?";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, 'ssdsiii', $_POST['name'], $_POST['description'], $_POST['price'], $imagePath, $_POST['category_id'], $_POST['stock'], $_POST['product_id']);
                    } else {
                        $query = "INSERT INTO products (name, description, price, image_url, category_id, stock) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, 'ssdsii', $_POST['name'], $_POST['description'], $_POST['price'], $imagePath, $_POST['category_id'], $_POST['stock']);
                    }
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "Error: " . mysqli_error($conn);
                    } else {
                        // Redirect to the products table view
                        header("Location: admin.php?table=products");
                        exit();
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "Error: Missing fields.";
                }
                break;
            
        case 'categories':
            // Handle add/edit categories
            if (isset($_POST['name'])) {
                if ($action == 'edit' && isset($_POST['category_id'])) {
                    $query = "UPDATE categories SET name = ? WHERE category_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 'si', $_POST['name'], $_POST['category_id']);
                } else {
                    $query = "INSERT INTO categories (name) VALUES (?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, 's', $_POST['name']);
                }
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Error: " . mysqli_error($conn);
                } else {
                    // Redirect to the categories table view
                    header("Location: admin.php?table=categories");
                    exit();
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Error: Missing fields.";
            }
            break;
            case 'orders':
                // Handle add/edit orders
                
            
        case 'cart':
            // Handle add/edit cart
            break;
        default:
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'add') {
    $fields = mysqli_fetch_fields(mysqli_query($conn, "SELECT * FROM $table LIMIT 1")); // Add this line
    ?>
    <div class="container">
    <h2>Add New <?php echo ucfirst($table); ?></h2>
        <div class="form-container">
            <form class="styled-form" action="admin.php?table=<?php echo $table; ?>" method="post" enctype="multipart/form-data">
                <?php foreach ($fields as $field) {
                    // Exclude specific fields from the form
                    if ($table == 'products' && $field->name == 'product_id') {
                        continue; // Skip product_id field
                    }
                    if ($table == 'users' && $field->name == 'password') {
                        continue; // Skip password field
                    }
                    if ($table == 'categories' && $field->name == 'category_id') {
                        continue; // Skip category_id field
                    }
                ?>
                    <div class="form-group">
                        <label><?php echo $field->name; ?>:</label>
                        <?php if ($field->name == 'image_url'): ?>
                            <input type="file" name="image">
                        <?php else: ?>
                            <input type="text" name="<?php echo $field->name; ?>" required>
                        <?php endif; ?>
                        
                    </div>
                <?php } ?>
                <input class="submit-btn" type="submit" value="Add New">
            </form>
        </div>
    </div>
<?php } elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) { 
    $primaryKeyField = fetchPrimaryKeyField($conn, $table);
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM $table WHERE $primaryKeyField = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    ?>
    <div class="container">
    <h2>Edit <?php echo ucfirst($table); ?></h2>
        <div class="form-container">
            <form class="styled-form" action="admin.php?table=<?php echo $table; ?>&action=edit" method="post" enctype="multipart/form-data">
                <?php foreach ($row as $key => $value) {
                    // Exclude specific fields from the form
                    if ($table == 'products' && $key == 'product_id') {
                        continue; // Skip product_id field
                    }
                    if ($table == 'users' && $key == 'password') {
                        continue; // Skip password field
                    }
                ?>
                     <div class="form-group">
                        <label><?php echo $key; ?>:</label> 
                        <?php if ($key == 'image_url'): ?>
                            <!-- Display existing image -->
                            <img src="<?php echo htmlspecialchars($value); ?>" alt="Product Image" style="max-width: 200px; display: block; margin-bottom: 10px;">
                            <input type="file" name="image">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($value); ?>">
                        <?php else: ?>
                            <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>" required>
                        <?php endif; ?>
                    </div>
                <?php } ?>
                <input type="hidden" name="<?php echo fetchPrimaryKeyField($conn, $table); ?>" value="<?php echo $id; ?>">
                <input class="submit-btn" type="submit" value="Update">
            </form>
        </div>
    </div>
<?php } 
 elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Handle delete request
    $id = $_GET['id'];

    if ($table == 'orders') {
        // First, delete related rows from order_items
        $query = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error deleting related order items: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    $query = "DELETE FROM $table WHERE " . fetchPrimaryKeyField($conn, $table) . " = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error: " . mysqli_error($conn);
    } else {
        // Redirect to the table view
        header("Location: admin.php?table=$table");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    if ($table) {
        ${$table} = fetchData($conn, $table);
        $primaryKeyField = fetchPrimaryKeyField($conn, $table);
    }
    // Fetch data with filter if search query is provided
if ($table) {
    if ($table == 'orders' && isset($_GET['search'])) {
        $search = mysqli_real_escape_string($conn, $_GET['search']);
        $query = "SELECT * FROM orders WHERE order_id LIKE '%$search%' OR customer_name LIKE '%$search%'";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            echo "Error: " . mysqli_error($conn);
            ${$table} = [];
        } else {
            ${$table} = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    } else {
        ${$table} = fetchData($conn, $table);
    }
    $primaryKeyField = fetchPrimaryKeyField($conn, $table);
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo ucfirst($table); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .navbar a:hover {
            background-color: #444;
            color: white;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #6c63ff;
        }
        h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #333;
        }
        .action-btns {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .action-btns a, .action-btns button {
            background-color: #6c63ff;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }
        .action-btns a:hover, .action-btns button:hover {
            background-color: #5548c8;
        }
        .navbar a.active {
            background-color: #444;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f2f2f2;
        }
        .action-btns a, .action-btns button {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .hidden {
            display: none;
        }
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-top: 5px solid #6c63ff;
}

h2 {
    font-size: 28px;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.form-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.styled-form {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
    display: block;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #6c63ff;
}

.submit-btn {
    background-color: #6c63ff;
    color: #fff;
    padding: 10px 20px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.submit-btn:hover {
    background-color: #5548c8;
}

        
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin.php?table=users">Users</a>
    <a href="admin.php?table=products">Products</a>
    <a href="admin.php?table=categories">Categories</a>
    <a href="admin.php?table=orders">Orders</a>
    <div class="logo" style="flex-grow: 1; text-align: center; margin-right: 280px;">
    <img src="sign/logo.png" alt="Company Logo" style="height: 50px; width: 260px;">
    </div>
</div>

<div class="container">
    <h1>Admin Panel - <?php echo ucfirst($table); ?></h1>
    <div class="action-btns">
    <?php if ($table != 'users' && $table != 'cart'): ?>
        <?php if ($table == 'orders'): ?>
            <form action="admin.php" method="get">
                <input type="hidden" name="table" value="orders">
                <input type="text" name="search" placeholder="Search Orders">
                <input type="submit" value="Filter">
            </form>
        <?php else: ?>
            <a href="admin.php?table=<?php echo $table; ?>&action=add">Add New</a>
        <?php endif; ?>
    <?php endif; ?>

    </div>

    <?php if ($table): ?>
        <table>
            <thead>
                <?php
                // Dynamically generate table headers
                $fields = mysqli_fetch_fields(mysqli_query($conn, "SELECT * FROM $table LIMIT 1"));
                echo '<tr>';
                foreach ($fields as $field) {
                    if ($table == 'users' && $field->name == 'password') {
                        continue; // Skip password field
                    }
                    elseif ($table == 'users' && $field->name == 'profile-pic') {
                        continue; // Skip profile-pic field
                    }
                    echo '<th>' . $field->name . '</th>';
                }
                echo '<th>Actions</th>';
                echo '</tr>';
                ?>
            </thead>
            <tbody>
                <?php
                // Dynamically generate table rows
                $data = ${$table}; // Access the variable with the same name as the table
                foreach ($data as $row) {
                    echo '<tr>';
                    foreach ($row as $key => $value) {
                        if ($table == 'users' && $key == 'password') {
                            continue; // Skip password field
                        }
                        elseif ($table == 'users' && $key == 'profile-pic') {
                            continue; // Skip password field
                        }
                        elseif ($key == 'invoice_path') { 
                            echo '<td><a href="'. htmlspecialchars($value) .'">View Invoice</a></td>';
                        } else {
                            echo '<td>'. htmlspecialchars($value) .'</td>';
                        }
                    }

                    echo '<td>';
                    if ($table != 'users') {
                        echo '<a href="admin.php?table=' . $table . '&action=edit&id=' . $row[fetchPrimaryKeyField($conn, $table)] . '">Edit</a> ';
                    }
                    echo '<a href="admin.php?table=' . $table . '&action=delete&id=' . $row[fetchPrimaryKeyField($conn, $table)] . '">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Please select a table to manage.</p>
    <?php endif; ?>
</div>

</body>
</html>