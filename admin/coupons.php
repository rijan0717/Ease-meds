


<?php
session_start();
include __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ease-meds/login.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['add_coupon'])) {
        $code      = $conn->real_escape_string($_POST['coupon_code']);
        $type      = $conn->real_escape_string($_POST['coupon_type']);
        $discount  = $_POST['discount_value'];
        $min_order = $_POST['min_order_amount'];
        $limit     = $_POST['usage_limit'];
        $expiry    = $_POST['expiry_date'];

        if ($type == "full") $discount = 0;

        $medicine_ids = "";
        if ($type == "medicines" && isset($_POST['medicine_ids'])) {
            $medicine_ids = $conn->real_escape_string(implode(',', $_POST['medicine_ids']));
        }

        $sql = "INSERT INTO coupons (coupon_code, coupon_type, discount_value, min_order_amount, usage_limit, expiry_date, medicine_ids)
                VALUES ('$code','$type','$discount','$min_order','$limit','$expiry','$medicine_ids')";

        if ($conn->query($sql)) { $msg = "✓ Coupon created successfully!"; $msg_type = "success"; }
        else                    { $msg = "Error: " . $conn->error;         $msg_type = "error";   }
    }

    if (isset($_POST['edit_coupon'])) {
        $id        = (int)$_POST['edit_id'];
        $code      = $conn->real_escape_string($_POST['edit_coupon_code']);
        $type      = $conn->real_escape_string($_POST['edit_coupon_type']);
        $discount  = (float)$_POST['edit_discount_value'];
        $min_order = (float)$_POST['edit_min_order_amount'];
        $limit     = (int)$_POST['edit_usage_limit'];
        $expiry    = $_POST['edit_expiry_date'];
        $status    = $conn->real_escape_string($_POST['edit_status']);

        if ($type == "full") $discount = 0;

        $medicine_ids = "";
        if ($type == "medicines" && isset($_POST['edit_medicine_ids']))
            $medicine_ids = $conn->real_escape_string(implode(',', $_POST['edit_medicine_ids']));

        $sql = "UPDATE coupons SET coupon_code='$code', coupon_type='$type', discount_value='$discount',
                min_order_amount='$min_order', usage_limit='$limit', expiry_date='$expiry',
                status='$status', medicine_ids='$medicine_ids' WHERE id='$id'";

        if ($conn->query($sql)) { $msg = "✓ Coupon updated successfully!"; $msg_type = "success"; }
        else                    { $msg = "Error: " . $conn->error;          $msg_type = "error";   }
    }

    if (isset($_POST['delete_coupon'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM coupons WHERE id='$id'");
        $msg = "✓ Coupon deleted successfully!"; $msg_type = "success";
    }
}

$medicines_result = $conn->query("SELECT id, name, price FROM medicines ORDER BY name ASC");
$all_medicines    = [];
while ($med = $medicines_result->fetch_assoc()) $all_medicines[] = $med;
$medicines_json = json_encode($all_medicines);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Coupons</title>
<link rel="stylesheet" href="/ease-meds/assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.form-row { display:flex; gap:15px; margin-bottom:15px; }
.form-row input, .form-row select { flex:1; padding:12px; border:1px solid #ddd; border-radius:6px; font-size:14px; }
.btn-delete { background:#fab1a0; color:#d63031; border:none; padding:6px 10px; cursor:pointer; border-radius:4px; }
.btn-edit   { background:#b2d8f7; color:#0984e3; border:none; padding:6px 10px; cursor:pointer; border-radius:4px; margin-right:4px; }
.btn-edit:hover { background:#74b9e8; }
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.active { display:flex; }
.modal-box { background:white; border-radius:10px; width:620px; max-width:95vw; max-height:90vh; overflow-y:auto; padding:30px; position:relative; animation:modalIn 0.2s ease; }
@keyframes modalIn { from { transform:scale(0.95); opacity:0; } to { transform:scale(1); opacity:1; } }
.modal-close { position:absolute; top:14px; right:18px; background:none; border:none; font-size:20px; cursor:pointer; color:#888; }
.modal-close:hover { color:#d63031; }
.modal-title { font-size:18px; font-weight:700; margin-bottom:20px; color:#333; }
#medicine-selector-panel, #edit-medicine-selector-panel { display:none; margin-bottom:15px; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; }
.med-panel-header { background:#f8f9fa; padding:12px 15px; font-weight:600; font-size:14px; color:#444; border-bottom:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center; }
.med-panel-header .selected-count { background:var(--primary-color,#0984e3); color:white; padding:2px 10px; border-radius:20px; font-size:12px; }
.med-search-bar { padding:10px 15px; border-bottom:1px solid #f0f0f0; }
.med-search-bar input { width:100%; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; box-sizing:border-box; }
.med-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); max-height:280px; overflow-y:auto; padding:10px 15px; }
.med-item { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:6px; cursor:pointer; font-size:13px; }
.med-item:hover { background:#f0f7ff; }
.med-item input[type="checkbox"] { accent-color:var(--primary-color,#0984e3); width:15px; height:15px; flex-shrink:0; }
.med-item .med-name  { flex:1; color:#333; font-size:13px; }
.med-item .med-price { color:#888; font-size:11px; white-space:nowrap; }
.med-select-actions { padding:10px 15px; border-top:1px solid #f0f0f0; display:flex; gap:10px; }
.med-select-actions button { padding:5px 14px; border-radius:5px; border:1px solid #ddd; background:white; font-size:12px; cursor:pointer; }
.med-select-actions button:hover { background:#f0f0f0; }
.discount-preview-section { background:white; padding:25px; border-radius:8px; margin-bottom:30px; }
.product-discount-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; }
.product-discount-card { border:1px solid #eee; border-radius:10px; overflow:hidden; position:relative; }
.product-discount-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.09); }
.discount-badge { position:absolute; top:10px; left:10px; background:#d63031; color:white; font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; z-index:2; }
.coupon-badge  { position:absolute; top:10px; right:10px; background:#6c5ce7; color:white; font-size:10px; font-weight:600; padding:3px 7px; border-radius:4px; z-index:2; }
.product-discount-card .product-img img { width:100%; height:130px; object-fit:cover; }
.product-discount-info { padding:12px; }
.product-discount-info .pname { font-weight:600; font-size:14px; color:#222; margin-bottom:5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.product-discount-info .prices { display:flex; align-items:center; gap:8px; }
.product-discount-info .original-price  { color:#999; font-size:12px; text-decoration:line-through; }
.product-discount-info .discounted-price { color:#d63031; font-weight:700; font-size:15px; }
.product-discount-info .coupon-info { margin-top:6px; font-size:11px; color:#6c5ce7; background:#f0eeff; display:inline-block; padding:2px 7px; border-radius:4px; }
.no-discount-notice { text-align:center; color:#aaa; padding:30px; font-size:14px; }
</style>
</head>
<body>
<div class="admin-container">
<?php include __DIR__ . '/sidebar.php'; ?>

<div class="main-content">
<h1>Manage Coupons</h1>

<?php if ($msg): ?>
<div class="alert alert-<?= ($msg_type ?? 'success') ?>"><?= $msg ?></div>
<?php endif; ?>

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

<div id="medicine-selector-panel">
    <div class="med-panel-header">
        <span><i class="fas fa-capsules" style="margin-right:7px;color:#0984e3;"></i>Select Applicable Medicines</span>
        <span class="selected-count" id="selectedCount">0 selected</span>
    </div>
    <div class="med-search-bar">
        <input type="text" id="medSearchInput" placeholder="🔍 Search medicines..." oninput="filterMedicines(this.value)">
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
    </div>
    <div class="med-select-actions">
        <button type="button" onclick="selectAllMeds()"><i class="fas fa-check-double"></i> Select All</button>
        <button type="button" onclick="clearAllMeds()"><i class="fas fa-times"></i> Clear All</button>
    </div>
</div>

<div class="form-row">
    <input type="number" step="0.01" name="discount_value"   id="discountValue"  placeholder="Discount Value">
    <input type="number" step="0.01" name="min_order_amount"                      placeholder="Minimum Order Amount">
</div>
<div class="form-row">
    <input type="number" name="usage_limit" placeholder="Usage Limit">
    <input type="date"   name="expiry_date">
</div>
<button type="submit" name="add_coupon"
    style="background:var(--primary-color);color:white;padding:12px 25px;border:none;border-radius:6px;cursor:pointer;">
    <i class="fas fa-plus"></i> Create Coupon
</button>
</form>
</div>

<div class="discount-preview-section">
    <h3><i class="fas fa-tags" style="color:#d63031;margin-right:8px;"></i>Products with Active Discounts</h3>
    <p style="color:#888;font-size:13px;margin-bottom:20px;">Medicines currently showing a discount badge based on active coupons</p>
    <div class="product-discount-grid">
    <?php
    $today = date('Y-m-d');
    $activeCoupons = $conn->query("SELECT * FROM coupons WHERE coupon_type='medicines'
                                   AND (expiry_date IS NULL OR expiry_date >= '$today')
                                   AND status='active' ORDER BY discount_value DESC");
    $medicineDiscountMap = [];
    if ($activeCoupons && $activeCoupons->num_rows > 0) {
        while ($coupon = $activeCoupons->fetch_assoc()) {
            if (!empty($coupon['medicine_ids'])) {
                foreach (explode(',', $coupon['medicine_ids']) as $mid) {
                    $mid = trim($mid);
                    if (!isset($medicineDiscountMap[$mid])) $medicineDiscountMap[$mid] = $coupon;
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
                $orig   = $med['price'];
                if ($coupon['coupon_type'] == 'percentage') {
                    $disc   = $orig - ($orig * $coupon['discount_value'] / 100);
                    $badge  = $coupon['discount_value'] . "% OFF";
                } elseif ($coupon['coupon_type'] == 'fixed') {
                    $disc   = max(0, $orig - $coupon['discount_value']);
                    $badge  = "Rs." . $coupon['discount_value'] . " OFF";
                } else { $disc = 0; $badge = "FREE"; }

                $imgUrl  = !empty($med['image'])
                    ? (strpos($med['image'], 'http') === 0 ? $med['image'] : '/ease-meds/' . $med['image'])
                    : '';
                $imgTag  = $imgUrl
                    ? '<img src="' . htmlspecialchars($imgUrl) . '" alt="">'
                    : '<div style="width:100%;height:130px;background:#f0f4f8;display:flex;align-items:center;justify-content:center;"><i class="fas fa-pills" style="font-size:40px;color:#b2bec3;"></i></div>';

                echo '<div class="product-discount-card">
                        <span class="discount-badge">' . $badge . '</span>
                        <span class="coupon-badge"><i class="fas fa-ticket-alt"></i> ' . htmlspecialchars($coupon['coupon_code']) . '</span>
                        <div class="product-img">' . $imgTag . '</div>
                        <div class="product-discount-info">
                            <div class="pname">' . htmlspecialchars($med['name']) . '</div>
                            <div class="prices">
                                <span class="original-price">Rs. ' . number_format($orig, 2) . '</span>
                                <span class="discounted-price">Rs. ' . number_format($disc, 2) . '</span>
                            </div>
                            <div class="coupon-info"><i class="fas fa-tag"></i> Use: ' . htmlspecialchars($coupon['coupon_code']) . '</div>
                        </div>
                      </div>';
            }
        } else {
            echo '<div class="no-discount-notice"><i class="fas fa-tag" style="font-size:30px;display:block;margin-bottom:10px;"></i>No medicines with active discounts found.</div>';
        }
    } else {
        echo '<div class="no-discount-notice"><i class="fas fa-tag" style="font-size:30px;display:block;margin-bottom:10px;"></i>No active medicine-specific coupons yet.</div>';
    }
    ?>
    </div>
</div>

<div style="background:white;padding:25px;border-radius:8px;">
<h3>Existing Coupons</h3>
<table class="admin-table">
<thead><tr><th>ID</th><th>Code</th><th>Type</th><th>Discount</th><th>Min Order</th><th>Medicines</th><th>Expiry</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
<?php
$result = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $cid      = $row['id'];
    $ccode    = htmlspecialchars($row['coupon_code'],   ENT_QUOTES);
    $ctype    = htmlspecialchars($row['coupon_type'],   ENT_QUOTES);
    $cdisc    = $row['discount_value'];
    $cmin     = $row['min_order_amount'];
    $climit   = $row['usage_limit'];
    $cexpiry  = $row['expiry_date'];
    $cstatus  = $row['status'];
    $cmeds    = $row['medicine_ids'];

    if ($ctype == 'percentage')  $discount = $cdisc . "%";
    elseif ($ctype == 'fixed')   $discount = "Rs. " . $cdisc;
    elseif ($ctype == 'full')    $discount = "Full Discount";
    else                         $discount = "Rs. " . $cdisc;

    $medCount = ($ctype == 'medicines' && !empty($cmeds))
        ? '<span style="background:#e0f0ff;color:#0984e3;padding:2px 8px;border-radius:4px;font-size:12px;">'
            . count(explode(',', $cmeds)) . ' medicine(s)</span>'
        : '<span style="color:#ccc;font-size:12px;">—</span>';

    echo "<tr>
        <td>$cid</td>
        <td><strong>$ccode</strong></td>
        <td>" . ucfirst($ctype) . "</td>
        <td>$discount</td>
        <td>Rs. $cmin</td>
        <td>$medCount</td>
        <td>$cexpiry</td>
        <td>$cstatus</td>
        <td style='white-space:nowrap;'>
            <button type='button' class='btn-edit'
                onclick='openEditModal($cid, \"$ccode\", \"$ctype\",
                    $cdisc, $cmin, $climit,
                    \"$cexpiry\", \"$cstatus\", \"$cmeds\")'>
                <i class='fas fa-edit'></i>
            </button>
            <form method='POST' style='display:inline;' onsubmit='return confirm(\"Delete this coupon?\");'>
                <input type='hidden' name='id' value='$cid'>
                <button type='submit' name='delete_coupon' class='btn-delete'><i class='fas fa-trash'></i></button>
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

<div class="modal-overlay" id="editModal">
<div class="modal-box">
    <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
    <div class="modal-title"><i class="fas fa-edit" style="color:#0984e3;margin-right:8px;"></i>Edit Coupon</div>
    <form method="POST">
    <input type="hidden" name="edit_id" id="edit_id">
    <div class="form-row">
        <input type="text" name="edit_coupon_code" id="edit_coupon_code" placeholder="Coupon Code" required>
        <select name="edit_coupon_type" id="edit_coupon_type" required onchange="toggleEditMedicinePanel(this.value)">
            <option value="fixed">Fixed Discount</option>
            <option value="percentage">Percentage Discount</option>
            <option value="full">Full Discount</option>
            <option value="medicines">Medicines (specific products)</option>
        </select>
    </div>
    <div id="edit-medicine-selector-panel">
        <div class="med-panel-header">
            <span><i class="fas fa-capsules" style="margin-right:7px;color:#0984e3;"></i>Select Applicable Medicines</span>
            <span class="selected-count" id="editSelectedCount">0 selected</span>
        </div>
        <div class="med-search-bar">
            <input type="text" id="editMedSearchInput" placeholder="🔍 Search medicines..." oninput="filterEditMedicines(this.value)">
        </div>
        <div class="med-grid" id="editMedicineGrid"></div>
        <div class="med-select-actions">
            <button type="button" onclick="editSelectAll()"><i class="fas fa-check-double"></i> Select All</button>
            <button type="button" onclick="editClearAll()"><i class="fas fa-times"></i> Clear All</button>
        </div>
    </div>
    <div class="form-row">
        <input type="number" step="0.01" name="edit_discount_value"   id="edit_discount_value"   placeholder="Discount Value">
        <input type="number" step="0.01" name="edit_min_order_amount" id="edit_min_order_amount" placeholder="Min Order Amount">
    </div>
    <div class="form-row">
        <input type="number" name="edit_usage_limit" id="edit_usage_limit" placeholder="Usage Limit">
        <input type="date"   name="edit_expiry_date" id="edit_expiry_date">
    </div>
    <div class="form-row">
        <select name="edit_status" id="edit_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <button type="submit" name="edit_coupon"
        style="background:var(--primary-color);color:white;padding:12px 25px;border:none;border-radius:6px;cursor:pointer;">
        <i class="fas fa-save"></i> Save Changes
    </button>
    <button type="button" onclick="closeEditModal()"
        style="background:#f0f0f0;color:#555;padding:12px 20px;border:none;border-radius:6px;cursor:pointer;margin-left:10px;">
        Cancel
    </button>
    </form>
</div>
</div>

<script>
const allMedicines = <?= $medicines_json ?? '[]' ?>;

function toggleMedicinePanel(type) {
    document.getElementById('medicine-selector-panel').style.display = (type==='medicines') ? 'block' : 'none';
    const dv = document.getElementById('discountValue');
    dv.disabled     = (type==='full');
    dv.placeholder  = (type==='full') ? 'N/A (Full discount)' : 'Discount Value';
    if (type!=='medicines') clearAllMeds();
}
function updateCount() {
    document.getElementById('selectedCount').textContent =
        document.querySelectorAll('#medicineGrid input:checked').length + ' selected';
}
function filterMedicines(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#medicineGrid .med-item').forEach(el => {
        el.style.display = (!q || el.querySelector('input').dataset.name.includes(q)) ? '' : 'none';
    });
}
function selectAllMeds() {
    document.querySelectorAll('#medicineGrid .med-item').forEach(el => {
        if (el.style.display!=='none') el.querySelector('input').checked = true;
    });
    updateCount();
}
function clearAllMeds() {
    document.querySelectorAll('#medicineGrid input').forEach(cb => cb.checked=false);
    updateCount();
}

function openEditModal(id,code,type,discount,minOrder,limit,expiry,status,medicineIds) {
    document.getElementById('edit_id').value              = id;
    document.getElementById('edit_coupon_code').value     = code;
    document.getElementById('edit_coupon_type').value     = type;
    document.getElementById('edit_discount_value').value  = discount;
    document.getElementById('edit_min_order_amount').value= minOrder;
    document.getElementById('edit_usage_limit').value     = limit;
    document.getElementById('edit_expiry_date').value     = expiry;
    document.getElementById('edit_status').value          = status;
    buildEditMedicineGrid(medicineIds);
    toggleEditMedicinePanel(type);
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}
document.getElementById('editModal').addEventListener('click', function(e) { if(e.target===this) closeEditModal(); });

function buildEditMedicineGrid(selectedIds) {
    const grid = document.getElementById('editMedicineGrid');
    const ids  = selectedIds ? selectedIds.split(',').map(s=>s.trim()) : [];
    grid.innerHTML = '';
    allMedicines.forEach(med => {
        const checked = ids.includes(String(med.id)) ? 'checked' : '';
        grid.innerHTML += `<label class="med-item" onclick="updateEditCount()">
            <input type="checkbox" name="edit_medicine_ids[]" value="${med.id}" data-name="${med.name.toLowerCase()}" ${checked}>
            <span class="med-name">${med.name}</span>
            <span class="med-price">Rs.${parseFloat(med.price).toFixed(2)}</span>
        </label>`;
    });
    updateEditCount();
}
function updateEditCount() {
    document.getElementById('editSelectedCount').textContent =
        document.querySelectorAll('#editMedicineGrid input:checked').length + ' selected';
}
function toggleEditMedicinePanel(type) {
    document.getElementById('edit-medicine-selector-panel').style.display = (type==='medicines') ? 'block' : 'none';
    document.getElementById('edit_discount_value').disabled = (type==='full');
}
function filterEditMedicines(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#editMedicineGrid .med-item').forEach(el => {
        el.style.display = (!q || el.querySelector('input').dataset.name.includes(q)) ? '' : 'none';
    });
}
function editSelectAll() {
    document.querySelectorAll('#editMedicineGrid .med-item').forEach(el => {
        if (el.style.display!=='none') el.querySelector('input').checked = true;
    });
    updateEditCount();
}
function editClearAll() {
    document.querySelectorAll('#editMedicineGrid input').forEach(cb => cb.checked=false);
    updateEditCount();
}
</script>
</body>
</html>
