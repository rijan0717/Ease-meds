<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'";
    if ($conn->query($update_sql)) {
        // Log in tracking table
        $desc = "Order status updated to " . ucfirst($new_status);
        $conn->query("INSERT INTO order_tracking (order_id, status_description) VALUES ('$order_id', '$desc')");
        $msg = "Order #$order_id updated successfully.";
    }
}

// Fetch Stats
$user_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$order_count = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$prescription_count = $conn->query("SELECT COUNT(*) as total FROM prescriptions")->fetch_assoc()['total'];
$medicine_count = $conn->query("SELECT COUNT(*) as total FROM medicines")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ease Meds</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <h1>Dashboard</h1>

            <?php if (isset($msg))
                echo "<div class='alert alert-success'>$msg</div>"; ?>

            <div class="dashboard-stats"
                style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); display:grid; gap:20px; margin-bottom:30px;">
                <div class="stat-card"
                    style="background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h3 style="margin:0; font-size:1rem; color:#666;">Total Users</h3>
                            <div class="stat-number"
                                style="font-size:2rem; font-weight:bold; color:var(--primary-color);">
                                <?php echo $user_count; ?></div>
                        </div>
                        <i class="fas fa-users" style="font-size:2.5rem; color:#dfe6e9;"></i>
                    </div>
                </div>
                <div class="stat-card"
                    style="background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h3 style="margin:0; font-size:1rem; color:#666;">Total Orders</h3>
                            <div class="stat-number"
                                style="font-size:2rem; font-weight:bold; color:var(--secondary-color);">
                                <?php echo $order_count; ?></div>
                        </div>
                        <i class="fas fa-shopping-bag" style="font-size:2.5rem; color:#dfe6e9;"></i>
                    </div>
                </div>
                <div class="stat-card"
                    style="background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h3 style="margin:0; font-size:1rem; color:#666;">Prescriptions</h3>
                            <div class="stat-number"
                                style="font-size:2rem; font-weight:bold; color:var(--accent-color);">
                                <?php echo $prescription_count; ?></div>
                        </div>
                        <i class="fas fa-file-invoice-medical" style="font-size:2.5rem; color:#dfe6e9;"></i>
                    </div>
                </div>
                <div class="stat-card"
                    style="background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h3 style="margin:0; font-size:1rem; color:#666;">Medicines</h3>
                            <div class="stat-number" style="font-size:2rem; font-weight:bold; color:#0984e3;">
                                <?php echo $medicine_count; ?></div>
                        </div>
                        <i class="fas fa-pills" style="font-size:2.5rem; color:#dfe6e9;"></i>
                    </div>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h2 style="margin:0;">Recent Orders</h2>
                    <a href="admin_orders.php" class="btn"
                        style="background:var(--primary-color); color:white; padding:10px 15px; border-radius:5px; text-decoration:none;">View
                        All Orders</a>
                </div>

                <?php
                $sql = "SELECT orders.id, users.username, orders.total_amount, orders.status, orders.created_at 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        ORDER BY orders.created_at DESC LIMIT 5";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='admin-table'>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $result->fetch_assoc()) {
                        $badge_class = 'badge-' . strtolower($row['status']);
                        echo "<tr>
                                <td>#" . $row['id'] . "</td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                <td>Rs. " . number_format($row['total_amount'], 2) . "</td>
                                <td><span class='badge $badge_class'>" . ucfirst($row['status']) . "</span></td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No orders found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>