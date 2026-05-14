<?php
session_start();
include __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ease-meds/login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: doctors.php"); exit(); }

$result = $conn->query("SELECT * FROM doctors WHERE id='$id'");
if ($result->num_rows == 0) { echo "Doctor not found."; exit(); }
$row = $result->fetch_assoc();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name       = $conn->real_escape_string($_POST['name']);
    $specialty  = $conn->real_escape_string($_POST['specialty']);
    $experience = $conn->real_escape_string($_POST['experience']);
    $bio_link   = $conn->real_escape_string($_POST['bio_link']);

    $image_update_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = __DIR__ . '/../uploads/doctors/';
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $name) . "." . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename)) {
            $rel_path         = 'uploads/doctors/' . $filename;
            $image_update_sql = ", image='$rel_path'";
        }
    }

    $sql = "UPDATE doctors SET name='$name', specialty='$specialty', experience='$experience', bio_link='$bio_link' $image_update_sql WHERE id='$id'";
    if ($conn->query($sql)) {
        $msg    = "Doctor updated successfully!";
        $result = $conn->query("SELECT * FROM doctors WHERE id='$id'");
        $row    = $result->fetch_assoc();
    } else {
        $msg = "Error: " . $conn->error;
    }
}

$current_img = !empty($row['image'])
    ? (strpos($row['image'], 'http') === 0 ? $row['image'] : '/ease-meds/' . $row['image'])
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor - Admin</title>
    <link rel="stylesheet" href="/ease-meds/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; margin-bottom:5px; font-weight:bold; }
        .form-group input { width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; }
        .preview-img { width:100px; height:100px; object-fit:cover; margin-top:10px; border-radius:50%; border:2px solid #ddd; }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <h1>Edit Doctor</h1>
        <?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>

        <div style="background:white;padding:40px;border-radius:8px;box-shadow:0 5px 15px rgba(0,0,0,0.1);max-width:600px;margin:0 auto;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Specialty</label>
                    <input type="text" name="specialty" value="<?php echo htmlspecialchars($row['specialty']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Experience</label>
                    <input type="text" name="experience" value="<?php echo htmlspecialchars($row['experience']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Profile Link (Optional)</label>
                    <input type="text" name="bio_link" value="<?php echo htmlspecialchars($row['bio_link']); ?>">
                </div>
                <div class="form-group">
                    <label>Doctor Photo</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($current_img): ?>
                        <div style="margin-top:10px;">
                            <p style="font-size:0.8rem;color:#666;">Current Photo:</p>
                            <img src="<?php echo $current_img; ?>" class="preview-img">
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn"
                    style="background:var(--primary-color);color:white;padding:12px 20px;border:none;cursor:pointer;width:100%;border-radius:6px;font-size:1rem;">
                    Update Doctor
                </button>
                <a href="doctors.php" style="display:block;text-align:center;margin-top:15px;color:#636e72;">
                    ← Back to Doctors
                </a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
