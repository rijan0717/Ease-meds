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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Ease Meds</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <h1>Manage Orders</h1>

            <?php if (isset($msg))
                echo "<div class='alert alert-success'>$msg</div>"; ?>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <?php
                // Pagination or extended list could be added here
                $sql = "SELECT orders.id, users.username, users.email, users.phone, orders.total_amount, orders.status, orders.created_at, orders.prescription_image 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        ORDER BY orders.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<div style='overflow-x: auto;'>
                          <table class='admin-table'>
                            <thead>
                                <tr>
                                    <th style='width: 5%;'>ID</th>
                                    <th style='width: 20%;'>User</th>
                                    <th style='width: 15%;'>Date</th>
                                    <th style='width: 15%;'>Total</th>
                                    <th style='width: 25%;'>Status</th>
                                    <th style='width: 10%; text-align:center;'>Prescription</th>
                                    <th style='width: 10%;'>Action</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = $result->fetch_assoc()) {
                        $status_options = ['pending', 'confirmed', 'packaging', 'shipped', 'delivered', 'cancelled'];
                        $options_html = "";
                        foreach ($status_options as $opt) {
                            $selected = ($row['status'] == $opt) ? 'selected' : '';
                            $options_html .= "<option value='$opt' $selected>" . ucfirst($opt) . "</option>";
                        }

                        // Determine badge class
                        $badge_class = 'badge-' . strtolower($row['status']);

                        echo "<tr>
                                <td>#" . $row['id'] . "</td>
                                <td>
                                    <div style='font-weight:600;'>" . htmlspecialchars($row['username']) . "</div>
                                    <div style='font-size:0.8rem; color:#888;'>" . htmlspecialchars($row['email']) . "</div>
                                    <div style='font-size:0.8rem; color:#666; font-weight:500;'><i class='fas fa-phone' style='font-size:0.7rem;'></i> " . (!empty($row['phone']) ? htmlspecialchars($row['phone']) : 'N/A') . "</div>
                                </td>
                                <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                <td style='font-weight:bold; color:var(--primary-color);'>Rs. " . number_format($row['total_amount'], 2) . "</td>
                                <td>
                                    <form method='POST' style='display:flex; flex-direction:column; gap:5px;'>
                                        <input type='hidden' name='order_id' value='" . $row['id'] . "'>
                                        <div style='display:flex; gap:5px;'>
                                            <span class='badge $badge_class' style='margin-bottom:5px; display:inline-block;'>" . ucfirst($row['status']) . "</span>
                                        </div>
                                        <div style='display:flex; gap:5px;'>
                                            <select name='status' style='padding:5px; border-radius:4px; font-size:0.9rem;'>
                                                $options_html
                                            </select>
                                            <button type='submit' name='update_status' class='btn' style='padding:5px 8px; font-size:0.8rem; background: var(--primary-color); color:white; border:none; border-radius:4px; cursor:pointer;'>
                                                <i class='fas fa-check'></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td style='text-align:center;'>
                                    " . (!empty($row['prescription_image']) ?
                            "<a href='" . htmlspecialchars($row['prescription_image']) . "' target='_blank' style='display:inline-flex; align-items:center; gap:5px; color:#3498db; text-decoration:none; background:#ecf0f1; padding:5px 10px; border-radius:20px; font-size:0.9rem;'>
                                        <i class='fas fa-file-prescription'></i> View
                                     </a>"
                            : "<span style='color:#ccc; font-style:italic;'>None</span>") . "
                                </td>
                                <td>
                                    <a href='track_order.php?order_id=" . $row['id'] . "' target='_blank' style='color:#2d3436; font-size:0.9rem; text-decoration:underline;'>
                                        Details
                                    </a>
                                </td>
                              </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<div style='text-align:center; padding:40px; color:#999;'>
                            <i class='fas fa-box-open' style='font-size:3rem; margin-bottom:15px;'></i>
                            <p>No orders found yet.</p>
                          </div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>