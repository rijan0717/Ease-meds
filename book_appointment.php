<?php
include 'header.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$doctor_name = isset($_GET['doctor']) ? $_GET['doctor'] : '';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $doctor = $_POST['doctor_name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $problem = $_POST['problem'];

    $sql = "INSERT INTO appointments (user_id, doctor_name, appointment_date, appointment_time, problem) 
            VALUES ('$user_id', '$doctor', '$date', '$time', '$problem')";

    if ($conn->query($sql)) {
        $msg = "Appointment booked successfully!";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>

<div class="container">
    <div class="form-container" style="max-width: 600px;">
        <h2 style="text-align: center;">Book Appointment</h2>
        <?php if ($msg)
            echo "<div class='alert alert-success'>$msg</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Doctor Name</label>
                <input type="text" name="doctor_name" value="<?php echo htmlspecialchars($doctor_name); ?>" required
                    <?php if ($doctor_name)
                        echo 'readonly'; ?> style="background: #f9f9f9;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="time" required>
                </div>
            </div>

            <div class="form-group">
                <label>Problem / Reason</label>
                <textarea name="problem" rows="4" required
                    style="width: 100%; border: 1px solid #ddd; padding: 10px; border-radius: 8px;"></textarea>
            </div>

            <button type="submit" class="btn-place-order">Confirm Booking</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>