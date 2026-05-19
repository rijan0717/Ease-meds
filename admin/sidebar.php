<div class="sidebar">
    <div class="sidebar-header">
        <a href="/ease-meds/admin/dashboard.php" class="logo">
            <i class="fas fa-heartbeat"></i> Ease Meds
        </a>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="/ease-meds/admin/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="/ease-meds/admin/orders.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i> Orders
            </a>
        </li>
        <li>
            <a href="/ease-meds/admin/medicines.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'medicines.php' ? 'active' : ''; ?>">
                <i class="fas fa-pills"></i> Medicines
            </a>
        </li>
        <li>
            <a href="/ease-meds/admin/doctors.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'doctors.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i> Doctors
            </a>
        </li>
        <li>
            <a href="/ease-meds/admin/users.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li>
            <a href="/ease-meds/admin/prescriptions.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'prescriptions.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-medical"></i> Prescriptions
            </a>
        </li>
        <li>
            <a href="/ease-meds/admin/coupons.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'coupons.php' ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i> Coupons
            </a>
        </li>
        <li>
            <?php
            $_sidebar_unread = 0;
            if (isset($conn)) {
                $_r = $conn->query("SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0");
                if ($_r) $_sidebar_unread = (int)$_r->fetch_assoc()['c'];
            }
            ?>
            <a href="/ease-meds/admin/contact_messages.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact_messages.php' ? 'active' : ''; ?>"
                style="display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fas fa-envelope"></i> Contact</span>
                <?php if ($_sidebar_unread > 0): ?>
                <span style="background:#e74c3c; color:#fff; font-size:0.7rem; font-weight:700; padding:2px 7px; border-radius:20px; min-width:20px; text-align:center;">
                    <?php echo $_sidebar_unread; ?>
                </span>
                <?php endif; ?>
            </a>
        </li>
        <li style="margin-top: auto;">
            <a href="/ease-meds/index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </li>
        <li>
            <a href="/ease-meds/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>
