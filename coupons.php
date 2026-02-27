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

        $sql = "INSERT INTO coupons
                (coupon_code, coupon_type, discount_value, min_order_amount, usage_limit, expiry_date)
                VALUES
                ('$code','$type','$discount','$min_order','$limit','$expiry')";

        if ($conn->query($sql)) {
            $msg = "Coupon created successfully!";
        } else {
            $msg = "Error: " . $conn->error;
        }
    }

    /* ================= DELETE COUPON ================= */
    if (isset($_POST['delete_coupon'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM coupons WHERE id='$id'");
        $msg = "Coupon deleted successfully!";
    }
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
.form-row{
    display:flex;
    gap:15px;
    margin-bottom:15px;
}

.form-row input,
.form-row select{
    flex:1;
    padding:12px;
    border:1px solid #ddd;
    border-radius:6px;
}

.btn-delete{
    background:#fab1a0;
    color:#d63031;
    border:none;
    padding:6px 10px;
    cursor:pointer;
    border-radius:4px;
}
</style>
</head>

<body>

<div class="admin-container">
<?php include 'admin_sidebar.php'; ?>

<div class="main-content">

<h1>Manage Coupons</h1>

<?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

<!-- ================= ADD COUPON ================= -->
<div style="background:white;padding:25px;border-radius:8px;margin-bottom:30px;">

<h3>Add New Coupon</h3>

<form method="POST">

<div class="form-row">
<input type="text" name="coupon_code" placeholder="Coupon Code (SAVE10)" required>

<select name="coupon_type" required>
    <option value="">Select Type</option>
    <option value="fixed">Fixed Discount</option>
    <option value="percentage">Percentage Discount</option>
    <option value="full">Full Discount</option>
</select>
</div>

<div class="form-row">
<input type="number" step="0.01" name="discount_value" placeholder="Discount Value">
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
<th>Expiry</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
$result = $conn->query("SELECT * FROM coupons ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {

if($row['coupon_type']=='percentage')
    $discount = $row['discount_value']."%";
elseif($row['coupon_type']=='fixed')
    $discount = "Rs. ".$row['discount_value'];
else
    $discount = "Full Discount";

echo "
<tr>
<td>{$row['id']}</td>
<td><strong>{$row['coupon_code']}</strong></td>
<td>".ucfirst($row['coupon_type'])."</td>
<td>$discount</td>
<td>Rs. {$row['min_order_amount']}</td>
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

</body>
</html>