<?php
include 'header.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$message = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["prescription"])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $description = $conn->real_escape_string($_POST['description']);
    $file_name = basename($_FILES["prescription"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["prescription"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $msg_type = "error";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $message = "Sorry, only JPG, JPEG, PNG files are allowed.";
        $msg_type = "error";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file)) {
            // Save to session cartridge
            $_SESSION['prescription_in_cart'] = [
                'path' => $target_file,
                'description' => $description
            ];

            // Redirect to cart
            echo "<script>window.location.href='cart.php';</script>";
            exit();

            /* 
            // OLD LOGIC: Insert directly to DB
            $user_id = $_SESSION['user_id'];
            $sql = "INSERT INTO prescriptions (user_id, image_path, description) VALUES ('$user_id', '$target_file', '$description')";

            if ($conn->query($sql) === TRUE) {
                $message = "Prescription uploaded successfully! We will review it shortly.";
                $msg_type = "success";
            } else {
                $message = "Database error: " . $conn->error;
                $msg_type = "error";
            }
            */
        } else {
            $message = "Sorry, there was an error uploading your file.";
            $msg_type = "error";
        }
    }
}
?>

<div class="container">
    <div class="auth-form" style="max-width: 600px;">
        <h2 style="text-align: center; margin-bottom: 20px;">Upload Prescription</h2>
        <p style="text-align: center; color: var(--text-light); margin-bottom: 30px;">
            Upload your doctor's prescription here. Our pharmacist will verify and process your order.
        </p>

        <?php if ($message): ?>
            <div style="padding: 15px; border-radius: 8px; margin-bottom: 20px; 
                background: <?php echo $msg_type == 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                color: <?php echo $msg_type == 'success' ? '#155724' : '#721c24'; ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">

            <!-- Image Upload & Preview -->
            <div class="form-group" style="text-align: center;">
                <div
                    style="width: 100%; height: 200px; border: 2px dashed #ddd; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; overflow: hidden; background: #f9f9f9; position: relative;">
                    <img id="preview-img" src="#" alt="Preview"
                        style="display: none; width: 100%; height: 100%; object-fit: contain;">
                    <div id="upload-placeholder">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #ccc;"></i>
                        <p style="color: #999;">Click to select image</p>
                    </div>
                    <input type="file" id="file-upload" name="prescription" required accept="image/*"
                        style="position: absolute; top:0; left:0; width:100%; height:100%; opacity: 0; cursor: pointer;"
                        onchange="previewImage(this)">
                </div>
            </div>

            <div class="form-group">
                <label>Additional Notes / Description</label>
                <textarea name="description" rows="3" placeholder="E.g., Please send 1 strip of Paracetamol..."
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;"></textarea>
            </div>

            <button type="submit" class="btn-place-order">
                <i class="fas fa-upload"></i> Upload Prescription
            </button>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        var preview = document.getElementById('preview-img');
        var placeholder = document.getElementById('upload-placeholder');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'footer.php'; ?>