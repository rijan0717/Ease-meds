<?php
include 'config.php';
// We don't include header here immediately because we might redirect or set headers before output.
// Although header.php output HTML so it's view content.
// We handle logic first.

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        // Combine country code and phone
        $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
        $full_phone = $country_code . preg_replace('/[^0-9]/', '', $phone);

        // Check if email already exists
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = $conn->query($check_email);

        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        }
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, phone, password) VALUES ('$username', '$email', '$full_phone', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                // $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                // Redirecting to login or showing success message. 
                // Let's show success message with a link.
                $success = "Registration successful! Redirecting to login...";
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
            }
            else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>
<?php include 'header.php'; ?>

<div class="container">
    <div class="auth-form">
        <h2 style="text-align: center; margin-bottom: 20px;">Create Account</h2>
        <p style="text-align: center; color: var(--text-light); margin-bottom: 30px;">Join Ease Meds today</p>

        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php
endif; ?>

        <?php if ($success): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $success; ?>
            </div>
        <?php
endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" required placeholder="Choose a username">
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Phone Number</label>
                <div style="display: flex; gap: 5px;">
                    <select name="country_code" required style="width: 120px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="+977">Nepal (+977)</option>
                        <option value="+91">India (+91)</option>
                        <option value="+1">USA (+1)</option>
                        <option value="+44">UK (+44)</option>
                        <option value="+61">Australia (+61)</option>
                        <option value="+86">China (+86)</option>
                        <option value="+81">Japan (+81)</option>
                        <option value="+49">Germany (+49)</option>
                        <option value="+33">France (+33)</option>
                        <option value="+7">Russia (+7)</option>
                        <option value="+971">UAE (+971)</option>
                        <option value="+92">Pakistan (+92)</option>
                        <option value="+88 Bangladesh (+880)</option>
                        <option value="+94">Sri Lanka (+94)</option>
                        <option value="+65">Singapore (+65)</option>
                        <option value="+60">Malaysia (+60)</option>
                        <option value="+66">Thailand (+66)</option>
                        <option value="+82">S. Korea (+82)</option>
                        <option value="+39">Italy (+39)</option>
                        <option value="+34">Spain (+34)</option>
                        <option value="+55">Brazil (+55)</option>
                        <option value="+27">S. Africa (+27)</option>
                        <option value="+20">Egypt (+20)</option>
                        <option value="+966">Saudi Arabia (+966)</option>
                    </select>
                    <input type="text" name="phone" required placeholder="Enter your mobile number" style="flex: 1;">
                </div>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" required placeholder="Create a password">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="Confirm your password">
            </div>
            <button type="submit" class="btn-place-order">Register</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: bold;">Login
                here</a>.
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>