<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// Form Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_doctor'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $specialty = $conn->real_escape_string($_POST['specialty']);
        $experience = $conn->real_escape_string($_POST['experience']);
        $bio_link = $conn->real_escape_string($_POST['bio_link']);

        // Check for image upload
        $target_dir = "uploads/doctors/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);

        $image = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $name) . "." . $ext;
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $target_file;
            }
        }

        $sql = "INSERT INTO doctors (name, specialty, experience, image, bio_link) VALUES ('$name', '$specialty', '$experience', '$image', '$bio_link')";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Doctor added successfully!";
        } else {
            $_SESSION['msg'] = "Error: " . $conn->error;
        }
        // Redirect to prevent duplicate submission
        header("Location: admin_doctors.php");
        exit();

    } elseif (isset($_POST['delete_doctor'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM doctors WHERE id='$id'");
        $_SESSION['msg'] = "Doctor removed successfully!";
        header("Location: admin_doctors.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Doctors - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .btn-action {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-right: 5px;
            display: inline-block;
        }

        .btn-edit {
            background: #dfe6e9;
            color: #2d3436;
        }

        .btn-edit:hover {
            background: #b2bec3;
        }

        .btn-delete {
            background: #fab1a0;
            color: #d63031;
            border: none;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #ff7675;
            color: white;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <h1>Manage Doctors</h1>
            <?php if ($msg)
                echo "<div class='alert alert-success'>$msg</div>"; ?>

            <!-- Add Doctor Form -->
            <div
                style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <h3
                    style="margin-bottom: 20px; color: var(--text-dark); border-bottom: 2px solid #f1f1f1; padding-bottom: 10px;">
                    Add New Doctor</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <input type="text" name="name" placeholder="Doctor Name (e.g. Dr. Ray)" required>
                        <input type="text" name="specialty" placeholder="Specialty (e.g. Cardiologist)" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="experience" placeholder="Experience (e.g. 10 Years)" required>
                        <input type="text" name="bio_link" placeholder="Profile Link (Optional)">
                    </div>
                    <div class="form-row">
                        <div style="flex: 1;">
                            <label
                                style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666; font-weight: 500;">Doctor
                                Photo</label>
                            <input type="file" name="image" accept="image/*" required
                                style="border: 1px solid #ddd; padding: 8px; width: 100%;">
                        </div>
                    </div>
                    <button type="submit" name="add_doctor" class="btn"
                        style="background: var(--primary-color); color: white; padding: 12px 25px; border: none; cursor: pointer; border-radius: 6px; font-weight: 600; font-size: 1rem;">
                        <i class="fas fa-plus"></i> Add Doctor
                    </button>
                </form>
            </div>

            <!-- Doctor List -->
            <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 20px; color: var(--text-dark);">Existing Doctors</h3>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th style="width: 80px;">Photo</th>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Experience</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM doctors ORDER BY id DESC");
                            while ($row = $result->fetch_assoc()) {
                                $img = !empty($row['image']) ? $row['image'] : 'https://via.placeholder.com/50';
                                echo "<tr>
                                <td>{$row['id']}</td>
                                <td><img src='$img' style='width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid #eee;'></td>
                                <td style='font-weight: 600;'>" . htmlspecialchars($row['name']) . "</td>
                                <td><span style='background: #e1f5fe; color: #0288d1; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;'>{$row['specialty']}</span></td>
                                <td>{$row['experience']}</td>
                                <td>
                                    <div style='display: flex; gap: 5px;'>
                                        <a href='admin_edit_doctor.php?id={$row['id']}' class='btn-action btn-edit'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <form method='POST' style='display:inline;' onsubmit='return confirm(\"Remove this doctor?\");'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <button type='submit' name='delete_doctor' class='btn-action btn-delete' title='Delete'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>