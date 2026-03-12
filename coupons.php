<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";

/* ================= ADD COUPON ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['add_coupon'])) {

        $code = $conn->real_escape_string($_POST['coupon_code']);
        $type = $conn->real_escape_string($_POST['coupon_type']);
        $discount = $_POST['discount_value'];
        $min_order = $_POST['min_order_amount'];
        $limit = $_POST['usage_limit'];
        $expiry = $_POST['expiry_date'];

        if ($type == "full") {
            $discount = 0;
        }

        // Handle medicine-specific coupon
        $medicine_ids = "";
        if ($type == "medicines" && isset($_POST['medicine_ids'])) {
            $medicine_ids = $conn->real_escape_string(implode(',', $_POST['medicine_ids']));
        }

        $sql = "INSERT INTO coupons
                (coupon_code, coupon_type, discount_value, min_order_amount, usage_limit, expiry_date, medicine_ids)
                VALUES
                ('$code','$type','$discount','$min_order','$limit','$expiry','$medicine_ids')";

        if ($conn->query($sql)) {
            $msg = "✓ Coupon created successfully!";
            $msg_type = "success";
        } else {
            $msg = "Error: " . $conn->error;
            $msg_type = "error";
        }
    }

    /* ================= DELETE COUPON ================= */
    if (isset($_POST['delete_coupon'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM coupons WHERE id='$id'");
        $msg = "✓ Coupon deleted successfully!";
        $msg_type = "success";
    }
}

// Fetch all medicines for the selector
$medicines_result = $conn->query("SELECT id, name, price FROM medicines ORDER BY name ASC");
$all_medicines = [];
while ($med = $medicines_result->fetch_assoc()) {
    $all_medicines[] = $med;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Coupons</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* ---- Layout ---- */
.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}
.form-row input,
.form-row select {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}
.btn-delete {
    background: #fab1a0;
    color: #d63031;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
    border-radius: 4px;
}

/* ---- Medicine Selector Panel ---- */
#medicine-selector-panel {
    display: none;
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}
.med-panel-header {
    background: #f8f9fa;
    padding: 12px 15px;
    font-weight: 600;
    font-size: 14px;
    color: #444;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.med-panel-header .selected-count {
    background: var(--primary-color, #0984e3);
    color: white;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 12px;
}
.med-search-bar {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
}
.med-search-bar input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    box-sizing: border-box;
}
.med-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0;
    max-height: 280px;
    overflow-y: auto;
    padding: 10px 15px;
}
.med-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.15s;
    font-size: 13px;
}
.med-item:hover {
    background: #f0f7ff;
}
.med-item input[type="checkbox"] {
    accent-color: var(--primary-color, #0984e3);
    width: 15px;
    height: 15px;
    flex-shrink: 0;
}
.med-item .med-name {
    flex: 1;
    color: #333;
    font-size: 13px;
}
.med-item .med-price {
    color: #888;
    font-size: 11px;
    white-space: nowrap;
}
.med-select-actions {
    padding: 10px 15px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 10px;
}
.med-select-actions button {
    padding: 5px 14px;
    border-radius: 5px;
    border: 1px solid #ddd;
    background: white;
    font-size: 12px;
    cursor: pointer;
    color: #555;
    transition: background 0.15s;
}
.med-select-actions button:hover {
    background: #f0f0f0;
}

/* ---- Discount Badges on Products (preview container) ---- */
.discount-preview-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 30px;
}
.discount-preview-section h3 {
    margin-bottom: 5px;
}
.discount-preview-section .subtitle {
    color: #888;
    font-size: 13px;
    margin-bottom: 20px;
}
.product-discount-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}
.product-discount-card {
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    transition: box-shadow 0.2s;
}
.product-discount-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.09);
}
.discount-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #d63031;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    letter-spacing: 0.3px;
    z-index: 2;
}
.coupon-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #6c5ce7;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 7px;
    border-radius: 4px;
    z-index: 2;
}
.product-discount-card .product-img {
    width: 100%;
    height: 130px;
    object-fit: cover;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: #ccc;
}
.product-discount-card .product-img img {
    width: 100%;
    height: 130px;
    object-fit: cover;
}
.product-discount-info {
    padding: 12px;
}
.product-discount-info .pname {
    font-weight: 600;
    font-size: 14px;
    color: #222;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.product-discount-info .prices {
    display: flex;
    align-items: center;
    gap: 8px;
}
.product-discount-info .original-price {
    color: #999;
    font-size: 12px;
    text-decoration: line-through;
}
.product-discount-info .discounted-price {
    color: #d63031;
    font-weight: 700;
    font-size: 15px;
}
.product-discount-info .coupon-info {
    margin-top: 6px;
    font-size: 11px;
    color: #6c5ce7;
    background: #f0eeff;
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
}
.no-discount-notice {
    text-align: center;
    color: #aaa;
    padding: 30px;
    font-size: 14px;
}
</style>
</head>

<body>

<div class="admin-container">
<?php include 'admin_sidebar.php'; ?>

<div class="main-content">

<h1>Manage Coupons</h1>

<?php if($msg): ?>
<div class="alert alert-<?= ($msg_type ?? 'success') ?>"><?= $msg ?></div>
<?php endif; ?>

<!-- ================= ADD COUPON ================= -->
<div style="background:white;padding:25px;border-radius:8px;margin-bottom:30px;">

<h3>Add New Coupon</h3>

<form method="POST" id="couponForm">

<div class="form-row">
    <input type="text" name="coupon_code" placeholder="Coupon Code (e.g. SAVE10)" required>
    <select name="coupon_type" id="couponType" required onchange="toggleMedicinePanel(this.value)">
        <option value="">Select Type</option>
        <option value="fixed">Fixed Discount</option>
        <option value="percentage">Percentage Discount</option>
        <option value="full">Full Discount</option>
        <option value="medicines">Medicines (specific products)</option>
    </select>
</div>

<!-- Medicine Selector Panel (shown only for 'medicines' type) -->
<div id="medicine-selector-panel">
    <div class="med-panel-header">
        <span><i class="fas fa-capsules" style="margin-right:7px;color:#0984e3;"></i>Select Applicable Medicines</span>
        <span class="selected-count" id="selectedCount">0 selected</span>
    </div>
    <div class="med-search-bar">
        <input type="text" id="medSearchInput" placeholder="🔍  Search medicines..." oninput="filterMedicines(this.value)">
    </div>
    <div class="med-grid" id="medicineGrid">
        <?php foreach ($all_medicines as $med): ?>
        <label class="med-item" onclick="updateCount()">
            <input type="checkbox" name="medicine_ids[]" value="<?= $med['id'] ?>"
                   data-name="<?= htmlspecialchars(strtolower($med['name'])) ?>">
            <span class="med-name"><?= htmlspecialchars($med['name']) ?></span>
            <span class="med-price">Rs.<?= number_format($med['price'], 2) ?></span>
        </label>
        <?php endforeach; ?>
        <?php if (empty($all_medicines)): ?>
        <div style="padding:20px;color:#aaa;font-size:13px;">No medicines found in the system.</div>
        <?php endif; ?>
    </div>
    <div class="med-select-actions">
        <button type="button" onclick="selectAllMeds()"><i class="fas fa-check-double"></i> Select All</button>
        <button type="button" onclick="clearAllMeds()"><i class="fas fa-times"></i> Clear All</button>
    </div>
</div>

<div class="form-row">
    <input type="number" step="0.01" name="discount_value" id="discountValue" placeholder="Discount Value">
    <input type="number" step="0.01" name="min_order_amount" placeholder="Minimum Order Amount">
</div>
<div class="form-row">
    <input type="number" name="usage_limit" placeholder="Usage Limit">
    <input type="date" name="expiry_date">
</div>
<button type="submit" name="add_coupon"
    style="background:var(--primary-color);color:white;padding:12px 25px;border:none;border-radius:6px;cursor:pointer;">
    <i class="fas fa-plus"></i> Create Coupon
</button>

</form>
</div>


<!-- ================= DISCOUNT PREVIEW ON PRODUCTS ================= -->
<div class="discount-preview-section">
    <h3><i class="fas fa-tags" style="color:#d63031;margin-right:8px;"></i>Products with Active Discounts</h3>
    <p class="subtitle">Medicines currently showing a discount badge to customers based on active coupons</p>

    <div class="product-discount-grid">
    <?php
    // Find active medicine-specific coupons
    $today = date('Y-m-d');
    $activeCouponsSql = "SELECT * FROM coupons
                         WHERE coupon_type = 'medicines'
                           AND (expiry_date IS NULL OR expiry_date >= '$today')
                           AND status = 'active'
                         ORDER BY discount_value DESC";
    $activeCoupons = $conn->query($activeCouponsSql);

    // Build a map: medicine_id => best coupon
    $medicineDiscountMap = [];
    if ($activeCoupons && $activeCoupons->num_rows > 0) {
        while ($coupon = $activeCoupons->fetch_assoc()) {
            if (!empty($coupon['medicine_ids'])) {
                $ids = explode(',', $coupon['medicine_ids']);
                foreach ($ids as $mid) {
                    $mid = trim($mid);
                    if (!isset($medicineDiscountMap[$mid])) {
                        $medicineDiscountMap[$mid] = $coupon;
                    }
                }
            }
        }
    }

    if (!empty($medicineDiscountMap)) {
        $idList = implode(',', array_map('intval', array_keys($medicineDiscountMap)));
        $medsWithDiscount = $conn->query("SELECT * FROM medicines WHERE id IN ($idList)");

        if ($medsWithDiscount && $medsWithDiscount->num_rows > 0) {
            while ($med = $medsWithDiscount->fetch_assoc()) {
                $coupon = $medicineDiscountMap[$med['id']];
                $originalPrice = $med['price'];

                if ($coupon['coupon_type'] == 'percentage') {
                    $discountedPrice = $originalPrice - ($originalPrice * $coupon['discount_value'] / 100);
                    $badgeText = $coupon['discount_value'] . "% OFF";
                } elseif ($coupon['coupon_type'] == 'fixed') {
                    $discountedPrice = max(0, $originalPrice - $coupon['discount_value']);
                    $badgeText = "Rs." . $coupon['discount_value'] . " OFF";
                } else {
                    $discountedPrice = 0;
                    $badgeText = "FREE";
                }

                $imgTag = !empty($med['image'])
                    ? '<img src="' . htmlspecialchars($med['image']) . '" alt="">'
                    : '<div style="width:100%;height:130px;background:#f0f4f8;display:flex;align-items:center;justify-content:center;"><i class="fas fa-pills" style="font-size:40px;color:#b2bec3;"></i></div>';

                echo '
                <div class="product-discount-card">
                    <span class="discount-badge">' . $badgeText . '</span>
                    <span class="coupon-badge"><i class="fas fa-ticket-alt"></i> ' . htmlspecialchars($coupon['coupon_code']) . '</span>
                    <div class="product-img">' . $imgTag . '</div>
                    <div class="product-discount-info">
                        <div class="pname">' . htmlspecialchars($med['name']) . '</div>
                        <div class="prices">
                            <span class="original-price">Rs. ' . number_format($originalPrice, 2) . '</span>
                            <span class="discounted-price">Rs. ' . number_format($discountedPrice, 2) . '</span>
                        </div>
                        <div class="coupon-info"><i class="fas fa-tag"></i> Use: ' . htmlspecialchars($coupon['coupon_code']) . '</div>
                    </div>
                </div>';
            }
        } else {
            echo '<div class="no-discount-notice"><i class="fas fa-tag" style="font-size:30px;display:block;margin-bottom:10px;"></i>No medicines with active discounts found.</div>';
        }
    } else {
        echo '<div class="no-discount-notice"><i class="fas fa-tag" style="font-size:30px;display:block;margin-bottom:10px;"></i>No active medicine-specific coupons yet.<br><small>Create a "Medicines" type coupon above to see products here.</small></div>';
    }
    ?>
    </div>
</div>


<!-- ================= COUPON LIST ================= -->
<div style="background:white;padding:25px;border-radius:8px;">

<h3>Existing Coupons</h3>

<table class="admin-table">
<thead>
<tr>
<th>ID</th>
<th>Code</th>
<th>Type</th>
<th>Discount</th>
<th>Min Order</th>
<th>Medicines</th>
<th>Expiry</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
$result = $conn->query("SELECT * FROM coupons ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {
    if ($row['coupon_type'] == 'percentage')
        $discount = $row['discount_value'] . "%";
    elseif ($row['coupon_type'] == 'fixed')
        $discount = "Rs. " . $row['discount_value'];
    elseif ($row['coupon_type'] == 'full')
        $discount = "Full Discount";
    else
        $discount = "Rs. " . $row['discount_value'];

    // Show medicine count if applicable
    $medCount = "";
    if ($row['coupon_type'] == 'medicines' && !empty($row['medicine_ids'])) {
        $count = count(explode(',', $row['medicine_ids']));
        $medCount = '<span style="background:#e0f0ff;color:#0984e3;padding:2px 8px;border-radius:4px;font-size:12px;">'
                  . $count . ' medicine' . ($count != 1 ? 's' : '') . '</span>';
    } else {
        $medCount = '<span style="color:#ccc;font-size:12px;">—</span>';
    }

    echo "
    <tr>
    <td>{$row['id']}</td>
    <td><strong>{$row['coupon_code']}</strong></td>
    <td>" . ucfirst($row['coupon_type']) . "</td>
    <td>$discount</td>
    <td>Rs. {$row['min_order_amount']}</td>
    <td>$medCount</td>
    <td>{$row['expiry_date']}</td>
    <td>{$row['status']}</td>
    <td>
    <form method='POST' onsubmit='return confirm(\"Delete this coupon?\");'>
    <input type='hidden' name='id' value='{$row['id']}'>
    <button type='submit' name='delete_coupon' class='btn-delete'>
    <i class='fas fa-trash'></i>
    </button>
    </form>
    </td>
    </tr>";
}
?>
</tbody>
</table>

</div>
</div>
</div>

<script>
function toggleMedicinePanel(type) {
    const panel = document.getElementById('medicine-selector-panel');
    const discountInput = document.getElementById('discountValue');

    if (type === 'medicines') {
        panel.style.display = 'block';
        discountInput.placeholder = 'Discount Value (for these medicines)';
    } else {
        panel.style.display = 'none';
        clearAllMeds();
    }

    if (type === 'full') {
        discountInput.value = '';
        discountInput.disabled = true;
        discountInput.placeholder = 'N/A (Full discount)';
    } else {
        discountInput.disabled = false;
        if (type !== 'medicines') discountInput.placeholder = 'Discount Value';
    }
}

function updateCount() {
    const checked = document.querySelectorAll('#medicineGrid input[type="checkbox"]:checked').length;
    document.getElementById('selectedCount').textContent = checked + ' selected';
}

function filterMedicines(query) {
    const items = document.querySelectorAll('#medicineGrid .med-item');
    const q = query.toLowerCase().trim();
    items.forEach(item => {
        const name = item.querySelector('input').dataset.name || '';
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
}

function selectAllMeds() {
    document.querySelectorAll('#medicineGrid .med-item').forEach(item => {
        if (item.style.display !== 'none') {
            item.querySelector('input').checked = true;
        }
    });
    updateCount();
}

function clearAllMeds() {
    document.querySelectorAll('#medicineGrid input[type="checkbox"]').forEach(cb => cb.checked = false);
    updateCount();
}
</script>

</body>
</html>