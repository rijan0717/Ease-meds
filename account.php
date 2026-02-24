<?php
include 'header.php';
include 'config.php'; // Assuming DB connection

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
// Ideally fetch more details from DB
?>

<div class="container">
    <h1 class="page-title">My Account</h1>

    <div class="account-container">
        <div
            style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
            <div
                style="width: 80px; height: 80px; background: #eef2f3; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--primary-color);">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h2>Hello, <?php echo htmlspecialchars($username); ?></h2>
                <p style="color: var(--text-light);">Member since <?php echo date('Y-m-d'); ?></p>
            </div>
        </div>

        <h3><i class="fas fa-history"></i> Order History</h3>
        <!-- Checking if order_placed flag is set from checkout -->
        <?php if (isset($_GET['order_placed'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> Order placed successfully! Thank you for shopping with Ease Meds.
            </div>
        <?php
endif; ?>

        <!-- Mock Orders or Fetch from DB -->
        <?php
// Simple fetch if table exists
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0):
?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>Rs. <?php echo $row['total_amount']; ?></td>
                                <td>
                                    <span
                                        style="padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; background: #ffeaa7; color: #d35400;">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="track_order.php?order_id=<?php echo $row['id']; ?>"
                                        style="color: var(--secondary-color); font-weight: bold; text-decoration: none;">
                                        <i class="fas fa-map-marker-alt"></i> Track
                                    </a>
                                </td>
                            </tr>
                        <?php
    endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php
else: ?>
            <p>You haven't placed any orders yet.</p>
        <?php
endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>