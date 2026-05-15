<?php
session_start();
include __DIR__ . '/includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart_empty          = !isset($_SESSION['cart']) || empty($_SESSION['cart']);
$prescription_exists = isset($_SESSION['prescription_in_cart']);

if ($cart_empty && !$prescription_exists) {
    echo "<script>alert('Your cart is empty.'); window.location.href='index.php';</script>";
    exit();
}

$user_id      = $_SESSION['user_id'];
$total_amount = 0;

if (!$cart_empty) {
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}

// Apply coupon discount
if (isset($_SESSION['applied_coupon'])) {
    $total_amount = max(0, $total_amount - (float)$_SESSION['applied_coupon']['discount']);
}

$payment_method = $_POST['payment'] ?? 'cod';
$payment_status = 'pending';
$transaction_id = null;

if ($payment_method === 'khalti' && isset($_POST['khalti_token'])) {
    $payment_status = 'paid';
    $transaction_id = $_POST['khalti_token'];
}

$prescription_path = null;

if (isset($_POST['use_session_prescription']) && isset($_SESSION['prescription_in_cart'])) {
    $prescription_path = $_SESSION['prescription_in_cart']['path'];
} elseif (isset($_FILES['prescription']) && $_FILES['prescription']['error'] == 0) {
    $target_dir = __DIR__ . '/uploads/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_ext = strtolower(pathinfo($_FILES['prescription']['name'], PATHINFO_EXTENSION));
    if (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
        $new_filename = "order_" . time() . "_" . $user_id . "." . $file_ext;
        $target_file  = $target_dir . $new_filename;
        if (move_uploaded_file($_FILES['prescription']['tmp_name'], $target_file)) {
            $prescription_path = 'uploads/' . $new_filename;
        }
    }
}

$sql  = "INSERT INTO orders (user_id, total_amount, payment_method, payment_status, transaction_id, status, prescription_image, created_at)
         VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idssss", $user_id, $total_amount, $payment_method, $payment_status, $transaction_id, $prescription_path);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;

    if (!$cart_empty) {
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, medicine_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt_item->execute();
        }
    }

    $conn->query("INSERT INTO order_tracking (order_id, status_description) VALUES ($order_id, 'Order placed successfully.')");

    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);
    unset($_SESSION['applied_coupon']);
    if (isset($_SESSION['prescription_in_cart'])) {
        unset($_SESSION['prescription_in_cart']);
    }

    echo "<script>alert('Order placed successfully!'); window.location.href='track_order.php?order_id=$order_id';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>
