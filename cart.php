<?php
include 'header.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

// Add to Cart Logic
if (isset($_POST['add_to_cart'])) {
    if (isset($_POST['medicine_id']) && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['quantity'])) {
        $id = $_POST['medicine_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
    } else {
        // Handle error or redirect if data is missing
        echo "<script>alert('Error: Missing item details.'); window.location.href='medicine.php';</script>";
        exit();
    }

    $item = array(
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity
    );

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Check if item already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['id'] == $id) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $item;
    }
}

// Remove Item Logic
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex
}

// Remove Prescription Logic
if (isset($_GET['remove_prescription'])) {
    unset($_SESSION['prescription_in_cart']);
    echo "<script>window.location.href='cart.php';</script>";
}

?>

<div class="container">
    <h1 class="page-title">Your Shopping Cart</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
            <div style="overflow-x: auto;"> <!-- Responsive scroll wrapper -->
                <table style="width: 100%; min-width: 600px; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee;">
                            <th style="padding: 15px; text-align: left; width: 40%;">Medicine</th>
                            <th style="padding: 15px; text-align: center; width: 15%;">Price</th>
                            <th style="padding: 15px; text-align: center; width: 15%;">Quantity</th>
                            <th style="padding: 15px; text-align: center; width: 15%;">Total</th>
                            <th style="padding: 15px; text-align: center; width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grand_total = 0;
                        foreach ($_SESSION['cart'] as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $grand_total += $subtotal;
                            ?>
                            <tr style="border-bottom: 1px solid #f9f9f9;">
                                <td style="padding: 15px; text-align: left; font-weight: 500; color: #333;">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td style="padding: 15px; text-align: center; color: #666;">Rs. <?php echo $item['price']; ?>
                                </td>
                                <td style="padding: 15px; text-align: center;"><?php echo $item['quantity']; ?></td>
                                <td style="padding: 15px; text-align: center; font-weight: bold; color: var(--primary-color);">
                                    Rs. <?php echo number_format($subtotal, 2); ?></td>
                                <td style="padding: 15px; text-align: center;">
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>"
                                        style="color: #e74c3c; font-weight: bold; text-decoration: none; font-size: 0.9rem;">
                                        <i class="fas fa-trash"></i> <span class="mobile-hide">Remove</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3"
                                style="padding: 20px; text-align: right; font-weight: bold; font-size: 1.2rem; color: #333;">
                                Grand Total:</td>
                            <td colspan="2"
                                style="padding: 20px; text-align: center; font-weight: bold; font-size: 1.3rem; color: var(--primary-color);">
                                Rs. <?php echo number_format($grand_total, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- End scroll wrapper -->

            <div style="margin-top: 20px; text-align: right;">
                <a href="index.php" class="btn-login" style="background: #95a5a6; margin-right: 10px;">Continue Shopping</a>
                <a href="checkout.php" class="btn-place-order"
                    style="display: inline-block; width: auto; text-decoration: none;">Proceed to Checkout</a>
            </div>
        </div>

        <!-- Prescription Attachment Section -->
    <?php elseif (isset($_SESSION['prescription_in_cart'])): ?>
        <div
            style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <h3>Attached Prescription</h3>
            <div
                style="display: flex; align-items: flex-start; gap: 20px; margin-top: 15px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <img src="<?php echo $_SESSION['prescription_in_cart']['path']; ?>" alt="Prescription"
                    style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                <div>
                    <h4 style="margin-bottom: 5px; color: #333;">Uploaded File</h4>
                    <p style="color: #444; font-weight: 500; font-size: 0.9rem; margin-bottom: 5px;">
                        <i class="fas fa-file-image"></i> <?php echo basename($_SESSION['prescription_in_cart']['path']); ?>
                    </p>
                    <p style="color: #666; margin-bottom: 15px;">
                        <?php echo !empty($_SESSION['prescription_in_cart']['description']) ? htmlspecialchars($_SESSION['prescription_in_cart']['description']) : 'No description provided'; ?>
                    </p>
                    <a href="cart.php?remove_prescription=true"
                        style="color: #e74c3c; font-weight: bold; text-decoration: none;">
                        <i class="fas fa-trash"></i> Remove Prescription
                    </a>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: right;">
                <a href="index.php" class="btn-login" style="background: #95a5a6; margin-right: 10px;">Continue Shopping</a>
                <a href="checkout.php" class="btn-place-order"
                    style="display: inline-block; width: auto; text-decoration: none;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart']) && !isset($_SESSION['prescription_in_cart'])): ?>
        <div style="text-align: center; padding: 50px;">
            <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <h3>Your cart is empty.</h3>
            <p>Looks like you haven't added any medicines yet.</p>
            <br>
            <a href="medicine.php" class="btn-place-order"
                style="display: inline-block; width: auto; text-decoration: none;">Browse Medicines</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>