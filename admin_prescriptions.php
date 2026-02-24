<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT prescriptions.id, users.username, prescriptions.image_path, prescriptions.created_at 
        FROM prescriptions 
        JOIN users ON prescriptions.user_id = users.id 
        ORDER BY prescriptions.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions - Ease Meds</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Ease Meds (Admin)</a>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Submitted Prescriptions</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="medicine-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="medicine-card">
                        <a href="<?php echo htmlspecialchars($row['image_path']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Prescription">
                        </a>
                        <div class="medicine-info">
                            <h3>Uploaded by:
                                <?php echo htmlspecialchars($row['username']); ?>
                            </h3>
                            <p>Date:
                                <?php echo $row['created_at']; ?>
                            </p>
                            <a href="<?php echo htmlspecialchars($row['image_path']); ?>" target="_blank" class="btn">View Full
                                Image</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No prescriptions uploaded yet.</p>
        <?php endif; ?>
    </div>
</body>

</html>