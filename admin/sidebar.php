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
