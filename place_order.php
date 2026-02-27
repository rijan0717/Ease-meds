<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if cart is empty AND no prescription in session
$cart_empty = !isset($_SESSION['cart']) || empty($_SESSION['cart']);
$prescription_exists = isset($_SESSION['prescription_in_cart']);

if ($cart_empty && !$prescription_exists) {
    echo "<script>alert('Your cart is empty.'); window.location.href='index.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$total_amount = 0;

// Calculate total
if (!$cart_empty) {
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}

// Payment info
$payment_method = $_POST['payment'] ?? 'cod';
$payment_status = 'pending';
$transaction_id = null;

if ($payment_method === 'khalti' && isset($_POST['khalti_token'])) {
    $payment_status = 'paid'; // For testing, assume success if token is present
    $transaction_id = $_POST['khalti_token'];
}

// Handle Prescription Upload
$prescription_path = null;

// Check if prescription is in session (from upload_prescription.php flow)
if (isset($_POST['use_session_prescription']) && isset($_SESSION['prescription_in_cart'])) {
    $prescription_path = $_SESSION['prescription_in_cart']['path'];
}
// Check for direct upload in checkout (fallback/original flow)
elseif (isset($_FILES['prescription']) && $_FILES['prescription']['error'] == 0) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["prescription"]["name"]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($file_ext, $allowed)) {
        $new_filename = "order_" . time() . "_" . $user_id . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file)) {
            $prescription_path = $target_file;
        }
    }
}

// Insert Order
$sql = "INSERT INTO orders (user_id, total_amount, payment_method, payment_status, transaction_id, status, prescription_image, created_at) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())";
$stmt = $conn->prepare($sql);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;

    // Insert Order Items ONLY if cart is not empty
    if (!$cart_empty) {
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_item->execute();

        // Optional: Update medicine stock quantity here if required
        // $conn->query("UPDATE medicines SET quantity = quantity - {$item['quantity']} WHERE id = {$item['id']}");
        }
    }

    // Initial tracking
    $conn->query("INSERT INTO order_tracking (order_id, status_description) VALUES ($order_id, 'Order placed successfully.')");

    // Clear cart
    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);
    // Clear prescription from session if it was used
    if (isset($_SESSION['prescription_in_cart'])) {
        unset($_SESSION['prescription_in_cart']);
    }

    // Redirect to tracking
    echo "<script>alert('Order placed successfully!'); window.location.href='track_order.php?order_id=$order_id';</script>";
}
else {
    echo "Error: " . $conn->error;
}
?>