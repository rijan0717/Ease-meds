<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ease Meds</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <header class="main-header">
        <div class="container header-container">
            <div class="logo-container">
                <a href="index.php" class="logo">
                    <i class="fas fa-heartbeat"></i> Ease Meds
                </a>
            </div>
            <nav class="main-nav">
                <ul class="nav-list">
                    <li><a href="medicine.php"><i class="fas fa-pills"></i> Medicine</a></li>
                    <li><a href="doctors.php"><i class="fas fa-user-md"></i> Doctors</a></li>
                    <li><a href="upload_prescription.php"><i class="fas fa-file-prescription"></i> Add Prescription</a>
                    </li>
                    <li><a href="cart.php" class="cart-link"><i class="fas fa-shopping-cart"></i> Cart</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="account.php"><i class="fas fa-user-circle"></i> My Account</a></li>
                        <li><a href="logout.php" class="btn-logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    <main>