<?php
include 'header.php';
?>
<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
<?php

// Mock cart total if empty
$total = isset($_SESSION['cart_total']) ? $_SESSION['cart_total'] : 0;
// If cart is handled via session array 'cart', calculate total
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<div class="container">
    <h1 class="page-title">Checkout</h1>

    <div class="checkout-container">
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-row">
                        <span>
                            <?php echo htmlspecialchars($item['name']); ?> (x
                            <?php echo $item['quantity']; ?>)
                        </span>
                        <span>Rs.
                            <?php echo $item['price'] * $item['quantity']; ?>
                        </span>
                    </div>
                <?php
    endforeach; ?>
            <?php
else: ?>
                <p>Your cart is empty.</p>
            <?php
endif; ?>

            <div class="summary-row total">
                <span>Total Amount</span>
                <span>Rs.
                    <?php echo $total; ?>
                </span>
            </div>
        </div>

        <!-- Coupon Section -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #eee;">
            <h4 style="margin-bottom: 10px;">Have a Coupon?</h4>
            <div style="display: flex; gap: 10px;">
                <input type="text" placeholder="Enter Coupon Code"
                    style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <button type="button" class="btn" onclick="alert('Coupon applied successfully! (Demo)')">Apply</button>
            </div>
        </div>

        <h3>Select Payment Method</h3>
        <form action="place_order.php" method="POST" enctype="multipart/form-data" id="checkoutForm">

            <div
                style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #eee;">
                <h4 style="margin-bottom: 10px;">Prescription (Optional)</h4>

                <?php if (isset($_SESSION['prescription_in_cart'])): ?>
                    <div
                        style="background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; display: flex; align-items: center; gap: 15px;">
                        <img src="<?php echo $_SESSION['prescription_in_cart']['path']; ?>" alt="Prescription"
                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        <div>
                            <p style="font-weight: 500; margin-bottom: 5px; color: #2ecc71;"><i
                                    class="fas fa-check-circle"></i> Prescription Attached</p>
                            <p style="font-size: 0.9rem; color: #666;">
                                <?php echo !empty($_SESSION['prescription_in_cart']['description']) ? htmlspecialchars($_SESSION['prescription_in_cart']['description']) : 'No notes added.'; ?>
                            </p>
                        </div>
                        <!-- Hidden input to signal backend -->
                        <input type="hidden" name="use_session_prescription" value="1">
                    </div>
                    <p style="font-size: 0.85rem; color: #888; margin-top: 5px;">To change this, please go back to cart and
                        remove the prescription.</p>

                <?php
else: ?>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">If your order contains medicines
                        requiring a prescription, please upload it here.</p>
                    <input type="file" name="prescription" id="prescriptionInput" accept="image/*"
                        style="border: 1px solid #ddd; padding: 10px; width: 100%; border-radius: 4px; margin-bottom: 10px;">

                    <!-- Preview Container -->
                    <div id="prescriptionPreviewContainer"
                        style="display: none; margin-top: 10px; position: relative; width: fit-content;">
                        <img id="prescriptionPreview" src="" alt="Prescription Preview"
                            style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px; display: block;">
                        <button type="button" id="removePrescription"
                            style="position: absolute; top: -10px; right: -10px; background: #ff4757; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                            &times;
                        </button>
                    </div>
                <?php
endif; ?>
            </div>

            <div class="payment-methods">
                <label class="payment-option esewa">
                    <input type="radio" name="payment" value="esewa" required>
                    <i class="fas fa-wallet fa-2x"></i>
                    <strong>eSewa</strong>
                </label>

                <label class="payment-option khalti">
                    <input type="radio" name="payment" value="khalti">
                    <i class="fas fa-mobile-alt fa-2x"></i>
                    <strong>Khalti</strong>
                </label>

                <label class="payment-option">
                    <a href=""></a>
                    <input type="radio" name="payment" value="cod">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                    <strong>Cash on Delivery</strong>
                </label>
            </div>
<a href="http://localhost/Ease-meds/index.php">
            <button type="submit" class="btn-place-order" id="placeOrderBtn">Place Order</button>
            </a>
        </form>
    </div>
</div>

<script>
    // Khalti Configuration
    var config = {
        // replace this key with yours
        "publicKey": "test_public_key_dc74e6689d0c4f31913c502d4d109189",
        "productIdentity": "EaseMeds-Order",
        "productName": "Ease Meds Medicines",
        "productUrl": "http://localhost/Ease-meds/",
        "paymentPreference": [
            "KHALTI",
            "EBANKING",
            "MOBILE_BANKING",
            "CONNECT_IPS",
            "SCT",
        ],
        "eventHandler": {
            onSuccess(payload) {
                // hit merchant api for initiating verfication
                console.log(payload);
                // Create hidden inputs for khalti token and submit form
                const form = document.getElementById('checkoutForm');
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = 'khalti_token';
                tokenInput.value = payload.token;
                form.appendChild(tokenInput);
                
                const amountInput = document.createElement('input');
                amountInput.type = 'hidden';
                amountInput.name = 'khalti_amount';
                amountInput.value = payload.amount;
                form.appendChild(amountInput);

                form.submit();
            },
            onError(error) {
                console.log(error);
                alert("Payment failed: " + error);
            },
            onClose() {
                console.log('widget is closing');
            }
        }
    };

    var checkout = new KhaltiCheckout(config);
    var btn = document.getElementById("placeOrderBtn");
    var form = document.getElementById('checkoutForm');

    form.onsubmit = function (e) {
        const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
        if (paymentMethod === 'khalti') {
            e.preventDefault();
            const amount = <?php echo $total * 100; ?>; // Amount in paisa
            checkout.show({ amount: amount });
        }
    };

    // Add visual selection effect
    const options = document.querySelectorAll('.payment-option input');
    options.forEach(opt => {
        opt.addEventListener('change', function () {
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            if (this.checked) {
                this.parentElement.classList.add('selected');
            }
        });
    });

    // Prescription Preview Logic
    const prescriptionInput = document.getElementById('prescriptionInput');
    const previewContainer = document.getElementById('prescriptionPreviewContainer');
    const previewImage = document.getElementById('prescriptionPreview');
    const removeButton = document.getElementById('removePrescription');

    if (prescriptionInput) {
        prescriptionInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                previewImage.src = '';
            }
        });

        removeButton.addEventListener('click', function () {
            prescriptionInput.value = ''; // Clear file input
            previewContainer.style.display = 'none';
            previewImage.src = '';
        });
    }
</script>

<?php include 'footer.php'; ?>