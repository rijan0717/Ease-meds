<div class="sidebar">
    <div class="sidebar-header">
        <a href="admin_dashboard.php" class="logo">
            <i class="fas fa-heartbeat"></i> Ease Meds
        </a>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="admin_dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="admin_orders.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_orders.php' ? 'active' : ''; ?>">
                Orders
            </a>
        </li>
        <li>
            <a href="admin_medicines.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_medicines.php' ? 'active' : ''; ?>">
                Medicines
            </a>
        </li>
        <li>
            <a href="admin_doctors.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_doctors.php' ? 'active' : ''; ?>">
                Doctors
            </a>
        </li>
        <li>
            <a href="admin_users.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'active' : ''; ?>">
                Users
            </a>
            <li>
            <a href="coupons.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'coupons.php' ? 'active' : ''; ?>">
                Coupons
            </a>
        </li>
        </li>
        <li style="margin-top: auto;">
            <a href="index.php" target="_blank">
                View Site
            </a>
        </li>
        <li>
            <a href="logout.php">
                Logout
            </a>
        </li>
    </ul>
</div>