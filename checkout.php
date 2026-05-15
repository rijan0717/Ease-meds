<?php
include __DIR__ . '/includes/header.php';

$cartTotal = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
}

$appliedDiscount    = 0;
$appliedCouponCode  = '';
$appliedCouponLabel = '';
$appliedItems       = [];

if (isset($_SESSION['applied_coupon'])) {
    $ac = $_SESSION['applied_coupon'];
    $appliedCouponCode  = $ac['code'];
    $appliedCouponLabel = $ac['label'];
    $appliedDiscount    = (float)$ac['discount'];
    $appliedItems       = $ac['applicable_items'] ?? [];
}

$finalTotal = max(0, $cartTotal - $appliedDiscount);
?>
<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

<div class="container">
    <h1 class="page-title">Checkout</h1>

    <div class="checkout-container">
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-row">
                        <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                        <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>

            <div class="summary-row" id="coupon-discount-row"
                 style="<?= $appliedDiscount > 0 ? '' : 'display:none;' ?> color:#27ae60;">
                <span><i class="fas fa-tag"></i> Coupon (<span id="applied-code"><?= htmlspecialchars($appliedCouponCode) ?></span>)</span>
                <span id="discount-amount">- Rs. <?= number_format($appliedDiscount, 2) ?></span>
            </div>

            <div class="summary-row total">
                <span>Total Amount</span>
                <span id="final-total">Rs. <?= number_format($finalTotal, 2) ?></span>
            </div>
        </div>

        <!-- Coupon Section -->
        <div style="background:white;padding:20px;border-radius:8px;margin-bottom:2rem;border:1px solid #eee;">
            <h4 style="margin-bottom:12px;"><i class="fas fa-ticket-alt" style="color:#6c5ce7;margin-right:6px;"></i>Have a Coupon?</h4>

            <div id="coupon-applied-info"
                 style="<?= $appliedDiscount > 0 ? 'display:flex;' : 'display:none;' ?> background:#eaffea;border:1px solid #b2dfdb;border-radius:6px;padding:12px;margin-bottom:10px;justify-content:space-between;align-items:center;">
                <div>
                    <span style="color:#27ae60;font-weight:600;"><i class="fas fa-check-circle"></i>
                        <span id="applied-code-banner"><?= htmlspecialchars($appliedCouponCode) ?></span></span>
                    <span style="color:#555;font-size:13px;margin-left:8px;" id="applied-label-banner"><?= htmlspecialchars($appliedCouponLabel) ?></span>
                </div>
                <button type="button" onclick="removeCoupon()"
                    style="background:none;border:none;color:#e74c3c;cursor:pointer;font-size:13px;"><i class="fas fa-times"></i> Remove</button>
            </div>

            <?php if (!empty($appliedItems)): ?>
            <div id="applicable-items-list" style="margin-bottom:10px;">
                <div style="font-size:12px;color:#555;background:#f9f9f9;padding:8px;border-radius:4px;">
                    <strong>Discount applied to:</strong>
                    <ul style="margin:4px 0 0 16px;padding:0;">
                        <?php foreach ($appliedItems as $ai): ?>
                        <li><?= htmlspecialchars($ai['name']) ?> — Rs. <?= number_format($ai['discount'], 2) ?> off</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php else: ?>
            <div id="applicable-items-list"></div>
            <?php endif; ?>

            <div id="coupon-input-section"
                 style="<?= $appliedDiscount > 0 ? 'display:none;' : 'display:flex;' ?> gap:10px;">
                <input type="text" id="couponCodeInput" placeholder="Enter coupon code"
                    style="flex:1;padding:10px;border:1px solid #ddd;border-radius:4px;text-transform:uppercase;font-size:14px;">
                <button type="button" onclick="applyCoupon()" id="applyCouponBtn"
                    style="padding:10px 20px;background:var(--primary-color);color:white;border:none;border-radius:4px;cursor:pointer;font-size:14px;">Apply</button>
            </div>
            <div id="coupon-message" style="margin-top:8px;font-size:13px;"></div>
        </div>

        <h3>Select Payment Method</h3>
        <form action="place_order.php" method="POST" enctype="multipart/form-data" id="checkoutForm">

            <div style="background:white;padding:20px;border-radius:8px;margin-bottom:2rem;border:1px solid #eee;">
                <h4 style="margin-bottom:10px;">Prescription (Optional)</h4>

                <?php if (isset($_SESSION['prescription_in_cart'])): ?>
                    <div style="background:#f8f9fa;padding:15px;border-radius:6px;border:1px solid #e9ecef;display:flex;align-items:center;gap:15px;">
                        <img src="<?php echo $_SESSION['prescription_in_cart']['path']; ?>" alt="Prescription"
                            style="width:80px;height:80px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">
                        <div>
                            <p style="font-weight:500;margin-bottom:5px;color:#2ecc71;"><i class="fas fa-check-circle"></i> Prescription Attached</p>
                            <p style="font-size:0.9rem;color:#666;">
                                <?php echo !empty($_SESSION['prescription_in_cart']['description']) ? htmlspecialchars($_SESSION['prescription_in_cart']['description']) : 'No notes added.'; ?>
                            </p>
                        </div>
                        <input type="hidden" name="use_session_prescription" value="1">
                    </div>
                    <p style="font-size:0.85rem;color:#888;margin-top:5px;">To change this, go back to cart and remove the prescription.</p>
                <?php else: ?>
                    <p style="font-size:0.9rem;color:#666;margin-bottom:10px;">If your order requires a prescription, please upload it here.</p>
                    <input type="file" name="prescription" id="prescriptionInput" accept="image/*"
                        style="border:1px solid #ddd;padding:10px;width:100%;border-radius:4px;margin-bottom:10px;">
                    <div id="prescriptionPreviewContainer" style="display:none;margin-top:10px;position:relative;width:fit-content;">
                        <img id="prescriptionPreview" src="" alt="Prescription Preview"
                            style="max-width:200px;max-height:200px;border:1px solid #ddd;border-radius:4px;display:block;">
                        <button type="button" id="removePrescription"
                            style="position:absolute;top:-10px;right:-10px;background:#ff4757;color:white;border:none;border-radius:50%;width:25px;height:25px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                            &times;
                        </button>
                    </div>
                <?php endif; ?>
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
                    <input type="radio" name="payment" value="cod">
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                    <strong>Cash on Delivery</strong>
                </label>
            </div>

            <button type="submit" class="btn-place-order" id="placeOrderBtn">Place Order</button>
        </form>
    </div>
</div>

<script>
const cartTotal = <?= (float)$cartTotal ?>;
let currentDiscount = <?= (float)$appliedDiscount ?>;

function applyCoupon() {
    const code = document.getElementById('couponCodeInput').value.trim();
    if (!code) return;

    const btn = document.getElementById('applyCouponBtn');
    btn.disabled = true;
    btn.textContent = 'Applying...';

    fetch('/ease-meds/apply_coupon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'coupon_code=' + encodeURIComponent(code)
    })
    .then(r => r.json())
    .then(data => {
        const msgEl = document.getElementById('coupon-message');
        if (data.success) {
            currentDiscount = data.discount;
            const newTotal  = data.new_total;

            document.getElementById('coupon-input-section').style.display  = 'none';
            document.getElementById('coupon-applied-info').style.display   = 'flex';
            document.getElementById('applied-code').textContent            = code.toUpperCase();
            document.getElementById('applied-code-banner').textContent     = code.toUpperCase();
            document.getElementById('applied-label-banner').textContent    = data.discount_label;

            document.getElementById('coupon-discount-row').style.display  = '';
            document.getElementById('discount-amount').textContent         = '- Rs. ' + parseFloat(data.discount).toFixed(2);
            document.getElementById('final-total').textContent             = 'Rs. ' + parseFloat(newTotal).toFixed(2);

            if (data.applicable_items && data.applicable_items.length > 0) {
                let html = '<div style="font-size:12px;color:#555;background:#f9f9f9;padding:8px;border-radius:4px;">';
                html += '<strong>Discount applied to:</strong><ul style="margin:4px 0 0 16px;padding:0;">';
                data.applicable_items.forEach(item => {
                    html += '<li>' + item.name + ' — Rs. ' + parseFloat(item.discount).toFixed(2) + ' off</li>';
                });
                html += '</ul></div>';
                document.getElementById('applicable-items-list').innerHTML = html;
            } else {
                document.getElementById('applicable-items-list').innerHTML = '';
            }

            msgEl.innerHTML = '<span style="color:#27ae60;"><i class="fas fa-check"></i> ' + data.message + '</span>';
        } else {
            msgEl.innerHTML = '<span style="color:#e74c3c;"><i class="fas fa-times-circle"></i> ' + data.message + '</span>';
        }
        btn.disabled   = false;
        btn.textContent = 'Apply';
    })
    .catch(() => {
        document.getElementById('coupon-message').innerHTML =
            '<span style="color:#e74c3c;">Error applying coupon. Please try again.</span>';
        btn.disabled    = false;
        btn.textContent = 'Apply';
    });
}

function removeCoupon() {
    fetch('/ease-meds/apply_coupon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'remove=1&coupon_code='
    }).then(() => {
        currentDiscount = 0;
        document.getElementById('coupon-applied-info').style.display  = 'none';
        document.getElementById('coupon-input-section').style.display = 'flex';
        document.getElementById('coupon-discount-row').style.display  = 'none';
        document.getElementById('applicable-items-list').innerHTML    = '';
        document.getElementById('coupon-message').innerHTML           = '';
        document.getElementById('couponCodeInput').value              = '';
        document.getElementById('final-total').textContent            = 'Rs. ' + cartTotal.toFixed(2);
    });
}

// Khalti
var config = {
    "publicKey": "test_public_key_dc74e6689d0c4f31913c502d4d109189",
    "productIdentity": "EaseMeds-Order",
    "productName": "Ease Meds Medicines",
    "productUrl": "http://localhost/Ease-meds/",
    "paymentPreference": ["KHALTI","EBANKING","MOBILE_BANKING","CONNECT_IPS","SCT"],
    "eventHandler": {
        onSuccess(payload) {
            const form = document.getElementById('checkoutForm');
            const t = document.createElement('input'); t.type='hidden'; t.name='khalti_token';  t.value=payload.token;  form.appendChild(t);
            const a = document.createElement('input'); a.type='hidden'; a.name='khalti_amount'; a.value=payload.amount; form.appendChild(a);
            form.submit();
        },
        onError(error)  { alert("Payment failed: " + error); },
        onClose()       { console.log('Khalti widget closed'); }
    }
};
var checkout = new KhaltiCheckout(config);

document.getElementById('checkoutForm').onsubmit = function(e) {
    const method = document.querySelector('input[name="payment"]:checked');
    if (!method) { e.preventDefault(); alert('Please select a payment method.'); return; }
    if (method.value === 'khalti') {
        e.preventDefault();
        const displayedTotal = parseFloat(
            document.getElementById('final-total').textContent.replace('Rs. ', '').replace(',', '')
        );
        checkout.show({ amount: Math.round(displayedTotal * 100) });
    }
};

document.querySelectorAll('.payment-option input').forEach(opt => {
    opt.addEventListener('change', function() {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
        if (this.checked) this.parentElement.classList.add('selected');
    });
});

const prescriptionInput = document.getElementById('prescriptionInput');
if (prescriptionInput) {
    const previewContainer = document.getElementById('prescriptionPreviewContainer');
    const previewImage     = document.getElementById('prescriptionPreview');
    const removeButton     = document.getElementById('removePrescription');

    prescriptionInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
            previewImage.src = '';
        }
    });

    removeButton.addEventListener('click', function() {
        prescriptionInput.value         = '';
        previewContainer.style.display  = 'none';
        previewImage.src                = '';
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
