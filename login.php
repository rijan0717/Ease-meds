<?php
session_start();
include __DIR__ . '/includes/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT id, username, password, role FROM users WHERE email = '$email'");

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']     = $row['role'];

            header("Location: " . ($row['role'] == 'admin' ? '/ease-meds/admin/dashboard.php' : '/ease-meds/index.php'));
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <div class="auth-form">
        <h2 style="text-align: center; margin-bottom: 20px;">Welcome Back</h2>

        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="passwordInput" required placeholder="Enter your password"
                           style="width: 100%; padding-right: 45px; box-sizing: border-box;">
                    <button type="button" id="togglePassword"
                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                               background: none; border: none; cursor: pointer; color: #999; font-size: 1rem; padding: 0;">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-place-order">Login</button>
        </form>

        <p style="text-align: center; margin-top: 15px;">
            Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Sign up here</a>.
        </p>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
