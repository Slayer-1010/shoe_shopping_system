<?php
session_start();
require_once 'db.php';
require('fpdf/fpdf.php'); // Include the FPDF library

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$userQuery = "SELECT username, email, phone, address FROM users WHERE user_id = $user_id";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

$subtotal = 0;
$shipping = 0;
$total = 0;

$query = "SELECT cart.*, products.name, products.price, cart.size FROM cart 
          JOIN products ON cart.product_id = products.product_id 
          WHERE cart.user_id = $user_id";
$result = mysqli_query($conn, $query);
$cartItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$total = $subtotal + $shipping;

// Insert order into orders table and get the order_id
$orderQuery = "INSERT INTO orders (customer_name, email, phone, address, total_amount, order_date) 
               VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = mysqli_prepare($conn, $orderQuery);
mysqli_stmt_bind_param($stmt, 'ssssi', $user['username'], $user['email'], $user['phone'], $user['address'], $total);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);

// Insert cart items into order_items table and update stock
foreach ($cartItems as $item) {
    $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price, size) 
                  VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $itemQuery);
    mysqli_stmt_bind_param($stmt, 'iiiii', $order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size']);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error inserting order item: " . mysqli_stmt_error($stmt);
        exit();
    }

    // Update stock in products table
    $updateStockQuery = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $updateStockQuery);
    mysqli_stmt_bind_param($stmt, 'ii', $item['quantity'], $item['product_id']);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error updating stock: " . mysqli_stmt_error($stmt);
        exit();
    }
}

// Clear the cart after checkout
$clearCartQuery = "DELETE FROM cart WHERE user_id = $user_id";
mysqli_query($conn, $clearCartQuery);

// Generate invoice PDF
class PDF extends FPDF {
    function Header() {
        // Add your own header content here
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function Invoice($user, $cartItems, $subtotal, $total) {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 12);
        
        // Center the logo at the top
        $this->Image('sign/logo.png', 80, 10, 50); // Adjust x coordinate (80) for centering the logo

        // Move the cursor to the left side and below the logo for the text
        $this->SetXY(10, 40); // Set X and Y position after the logo, X=10 for left alignment

        // User details
        $this->Cell(0, 10, "Invoice for: " . $user['username'], 0, 1);
        $this->Cell(0, 10, "Email: " . $user['email'], 0, 1);
        $this->Cell(0, 10, "Phone: " . $user['phone'], 0, 1);
        $this->Cell(0, 10, "Address: " . $user['address'], 0, 1);
        $this->Ln(20); // Add some space before the table

        // Table header
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 10, 'Product Name', 1);
        $this->Cell(20, 10, 'Quantity', 1);
        $this->Cell(20, 10, 'Size', 1);
        $this->Cell(30, 10, 'Price', 1);
        $this->Cell(30, 10, 'Total', 1); // New Total column
        $this->Ln();

        // Table content
        $this->SetFont('Arial', '', 10);
        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity']; // Calculate total for each item
            $this->Cell(50, 10, $item['name'], 1);
            $this->Cell(20, 10, $item['quantity'], 1);
            $this->Cell(20, 10, $item['size'], 1);
            $this->Cell(30, 10, "Rs." . number_format($item['price'], 2), 1);
            $this->Cell(30, 10, "Rs." . number_format($itemTotal, 2), 1); // Display item total
            $this->Ln();
        }

        // Totals
        $this->Ln();
        $this->Cell(50, 10, '', 0);
        $this->Cell(20, 10, '', 0);
        $this->Cell(20, 10, '', 0);
        $this->Cell(30, 10, 'Subtotal:', 1);
        $this->Cell(30, 10, "Rs." . number_format($subtotal, 2), 1);
        $this->Ln();
        $this->Cell(50, 10, '', 0);
        $this->Cell(20, 10, '', 0);
        $this->Cell(20, 10, '', 0);
        $this->Cell(30, 10, 'Total:', 1);
        $this->Cell(30, 10, "Rs.." . number_format($total, 2), 1);
    }
}

// Create invoice directory if it doesn't exist
if (!file_exists('invoices')) {
    mkdir('invoices', 0777, true);
}

// Create the PDF
$pdf = new PDF();
$pdf->Invoice($user, $cartItems, $subtotal, $total);
$invoicePath = 'invoices/invoice_' . $order_id . '.pdf';
$pdf->Output('F', $invoicePath);

// Update the order with the invoice path
$updateOrderQuery = "UPDATE orders SET invoice_path = ? WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $updateOrderQuery);
mysqli_stmt_bind_param($stmt, 'si', $invoicePath, $order_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Redirect to order confirmation page with invoice link
header("Location: confirmation.php?invoice=" . urlencode($invoicePath));
exit();
?>
