<?php
include 'header.php';
include 'config.php';

if (!isset($_GET['order_id'])) {
    echo "<div class='container'><p>Order ID missing.</p></div>";
    include 'footer.php';
    exit();
}

$order_id = $_GET['order_id'];
$sql = "SELECT * FROM orders WHERE id = '$order_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div class='container'><p>Order not found.</p></div>";
    include 'footer.php';
    exit();
}

$order = $result->fetch_assoc();
$status = $order['status'];

// Timeline configuration
$steps = ['pending', 'confirmed', 'packaging', 'shipped', 'delivered'];
$current_step_index = array_search($status, $steps);
if ($current_step_index === false && $status == 'cancelled') {
    $current_step_index = -1; // Cancelled
}
?>

<div class="container">
    <h1 class="page-title">Track Order #
        <?php echo $order_id; ?>
    </h1>

    <div
        style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 40px;">
        <?php if ($status == 'cancelled'): ?>
            <div style="color: red; font-weight: bold; text-align: center; font-size: 1.5rem;">
                <i class="fas fa-times-circle"></i> This order has been cancelled.
            </div>
        <?php else: ?>
            <div class="tracking-progress-bar">
                <?php foreach ($steps as $index => $step):
                    $active = $index <= $current_step_index ? 'active' : '';
                    ?>
                    <div class="step <?php echo $active; ?>">
                        <div class="icon">
                            <?php
                            if ($step == 'pending')
                                echo '<i class="fas fa-hourglass-start"></i>';
                            if ($step == 'confirmed')
                                echo '<i class="fas fa-check-circle"></i>';
                            if ($step == 'packaging')
                                echo '<i class="fas fa-box"></i>';
                            if ($step == 'shipped')
                                echo '<i class="fas fa-truck"></i>';
                            if ($step == 'delivered')
                                echo '<i class="fas fa-home"></i>';
                            ?>
                        </div>
                        <div class="label">
                            <?php echo ucfirst($step); ?>
                        </div>
                    </div>
                    <?php if ($index < count($steps) - 1): ?>
                        <div class="line <?php echo $index < $current_step_index ? 'active' : ''; ?>"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tracking History Log -->
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3>Tracking History</h3>
        <?php
        $log_sql = "SELECT * FROM order_tracking WHERE order_id = '$order_id' ORDER BY updated_at DESC";
        $log_result = $conn->query($log_sql);

        if ($log_result && $log_result->num_rows > 0) {
            echo "<ul style='list-style: none; padding: 0;'>";
            while ($log = $log_result->fetch_assoc()) {
                echo "<li style='border-left: 2px solid #ddd; padding-left: 20px; margin-bottom: 15px; position: relative;'>
                        <div style='font-size: 0.9rem; color: #888;'>" . $log['updated_at'] . "</div>
                        <div style='font-weight: bold;'>" . htmlspecialchars($log['status_description']) . "</div>
                        <span style='position: absolute; left: -6px; top: 0; width: 10px; height: 10px; background: #bbb; border-radius: 50%;'></span>
                      </li>";
            }
            echo "</ul>";
        } else {
            // Default first log
            echo "<p>Order created on " . $order['created_at'] . "</p>";
        }
        ?>
    </div>
</div>

<style>
    .tracking-progress-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        padding: 20px 0;
    }

    .step {
        text-align: center;
        z-index: 2;
        background: white;
        padding: 0 10px;
    }

    .step .icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #888;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    .step.active .icon {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 10px rgba(0, 184, 148, 0.3);
    }

    .step .label {
        font-size: 0.9rem;
        color: #888;
        font-weight: 500;
    }

    .step.active .label {
        color: var(--primary-color);
        font-weight: bold;
    }

    .line {
        flex-grow: 1;
        height: 3px;
        background: #e0e0e0;
        position: relative;
        top: -15px;
        z-index: 1;
    }

    .line.active {
        background: var(--primary-color);
    }
</style>

<?php include 'footer.php'; ?>