<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_doctors.php");
    exit();
}

// Get doctor details
$result = $conn->query("SELECT * FROM doctors WHERE id='$id'");
if ($result->num_rows == 0) {
    echo "Doctor not found.";
    exit();
}
$row = $result->fetch_assoc();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $specialty = $conn->real_escape_string($_POST['specialty']);
    $experience = $conn->real_escape_string($_POST['experience']);
    $bio_link = $conn->real_escape_string($_POST['bio_link']);

    // Check for new image
    $image_update_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/doctors/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $name) . "." . $ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_update_sql = ", image='$target_file'";
        }
    }

    $sql = "UPDATE doctors SET 
            name='$name', 
            specialty='$specialty', 
            experience='$experience', 
            bio_link='$bio_link' 
            $image_update_sql 
            WHERE id='$id'";

    if ($conn->query($sql)) {
        $msg = "Doctor updated successfully!";
        // Refresh data
        $result = $conn->query("SELECT * FROM doctors WHERE id='$id'");
        $row = $result->fetch_assoc();
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Doctor - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .preview-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <a href="admin_dashboard.php" class="logo">Admin Panel</a>
            <ul class="nav-links">
                <li><a href="admin_doctors.php">Back to Doctors</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1>Edit Doctor</h1>
        <?php if ($msg)
            echo "<div class='alert alert-success'>$msg</div>"; ?>

        <div
            style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Specialty</label>
                    <input type="text" name="specialty" value="<?php echo htmlspecialchars($row['specialty']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Experience</label>
                    <input type="text" name="experience" value="<?php echo htmlspecialchars($row['experience']); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Profile Link (Optional)</label>
                    <input type="text" name="bio_link" value="<?php echo htmlspecialchars($row['bio_link']); ?>">
                </div>

                <div class="form-group">
                    <label>Doctor Photo</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if (!empty($row['image'])): ?>
                        <div style="margin-top: 10px;">
                            <p style="font-size: 0.8rem; color: #666;">Current Photo:</p>
                            <img src="<?php echo $row['image']; ?>" class="preview-img">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn"
                    style="background: var(--primary-color); color: white; padding: 12px 20px; border: none; cursor: pointer; width: 100%; border-radius: 6px; font-size: 1rem;">Update
                    Doctor</button>
            </form>
        </div>
    </div>
</body>

</html>