<?php
session_start();
include __DIR__ . '/includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

if (isset($_POST['remove'])) {
    unset($_SESSION['applied_coupon']);
    echo json_encode(['success' => true, 'removed' => true]);
    exit();
}

$code = trim($_POST['coupon_code'] ?? '');
if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a coupon code.']);
    exit();
}

$escaped = $conn->real_escape_string($code);
$today   = date('Y-m-d');

$res = $conn->query("SELECT * FROM coupons
    WHERE coupon_code = '$escaped'
    AND status = 'active'
    AND (expiry_date IS NULL OR expiry_date >= '$today')");

if (!$res || $res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code.']);
    exit();
}

$coupon = $res->fetch_assoc();
$cart   = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit();
}

$cartTotal = 0;
foreach ($cart as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

if (!empty($coupon['min_order_amount']) && $cartTotal < (float)$coupon['min_order_amount']) {
    echo json_encode(['success' => false,
        'message' => 'Minimum order of Rs. ' . number_format($coupon['min_order_amount'], 2) . ' required for this coupon.']);
    exit();
}

$discount        = 0;
$applicableItems = [];
$discountLabel   = '';

switch ($coupon['coupon_type']) {
    case 'fixed':
        $discount      = min((float)$coupon['discount_value'], $cartTotal);
        $discountLabel = 'Rs. ' . number_format($coupon['discount_value'], 2) . ' off entire order';
        break;

    case 'percentage':
        $discount      = round($cartTotal * (float)$coupon['discount_value'] / 100, 2);
        $discountLabel = $coupon['discount_value'] . '% off entire order';
        break;

    case 'full':
        $discount      = $cartTotal;
        $discountLabel = '100% off (full discount)';
        break;

    case 'medicines':
        $allowedIds = array_map('trim', explode(',', $coupon['medicine_ids'] ?? ''));
        $pct        = (float)$coupon['discount_value'];
        foreach ($cart as $item) {
            if (in_array((string)$item['id'], $allowedIds)) {
                $sub          = $item['price'] * $item['quantity'];
                $itemDiscount = $pct > 0 ? round($sub * $pct / 100, 2) : $sub;
                $discount    += $itemDiscount;
                $applicableItems[] = ['name' => $item['name'], 'discount' => $itemDiscount];
            }
        }
        if (empty($applicableItems)) {
            echo json_encode(['success' => false,
                'message' => 'None of your cart items are eligible for this coupon.']);
            exit();
        }
        $discountLabel = ($pct > 0 ? $pct . '% off' : 'Free') . ' on selected medicines';
        break;
}

$newTotal = max(0, round($cartTotal - $discount, 2));

$_SESSION['applied_coupon'] = [
    'code'             => $coupon['coupon_code'],
    'discount'         => $discount,
    'label'            => $discountLabel,
    'applicable_items' => $applicableItems,
];

echo json_encode([
    'success'          => true,
    'message'          => 'Coupon applied!',
    'discount'         => $discount,
    'discount_label'   => $discountLabel,
    'new_total'        => $newTotal,
    'applicable_items' => $applicableItems,
]);
