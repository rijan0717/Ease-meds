<?php
include 'config.php';
// Start session if not already started (handled in header, but good to be safe if header not included yet, though here we include header)
// Actually we need session start before header usually if we use session logic inside header.
// header.php handles session_start check.

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, role FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Since dummy data might not be hashed or simple setup, check if verify works or direct compare if needed (for safety we use verify, assume DB has hash)
        // If the user manually inserted plain text, this fails. I should warn or maybe allow plain text if verify fails for debug (NOT SECURE but common in student projects).
        // For now sticking to password_verify.
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>
<!-- Include header AFTER session start logic if possible, but header starts session too. 
     If we process POST before header, we need to start session manually if we set session vars. 
     But session_start() is idempotent-ish with checks. 
     Let's just include header for the view. -->
<?php include 'header.php'; ?>

<div class="container">
    <div class="auth-form">
        <h2 style="text-align: center; margin-bottom: 20px;">Welcome Back</h2>

        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn-place-order">Login</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Sign
                up here</a>.
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>