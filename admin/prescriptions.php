<?php
session_start();
include __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ease-meds/login.php");
    exit();
}

$sql    = "SELECT prescriptions.id, users.username, prescriptions.image_path, prescriptions.created_at
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
    <title>Prescriptions - Ease Meds Admin</title>
    <link rel="stylesheet" href="/ease-meds/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="admin-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <h1>Submitted Prescriptions</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="medicine-grid">
                <?php while ($row = $result->fetch_assoc()):
                    $img_src = strpos($row['image_path'], 'http') === 0
                        ? $row['image_path']
                        : '/ease-meds/' . $row['image_path'];
                ?>
                    <div class="medicine-card">
                        <a href="<?php echo htmlspecialchars($img_src); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Prescription">
                        </a>
                        <div class="medicine-info">
                            <h3>Uploaded by: <?php echo htmlspecialchars($row['username']); ?></h3>
                            <p>Date: <?php echo $row['created_at']; ?></p>
                            <a href="<?php echo htmlspecialchars($img_src); ?>" target="_blank" class="btn">View Full Image</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center;padding:60px;color:#999;">
                <i class="fas fa-file-medical" style="font-size:3rem;margin-bottom:15px;display:block;"></i>
                <p>No prescriptions uploaded yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
