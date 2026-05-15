<?php
session_start();
include __DIR__ . '/includes/config.php';

$error   = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username         = $conn->real_escape_string($_POST['username']);
    $email            = $conn->real_escape_string($_POST['email']);
    $phone            = $conn->real_escape_string($_POST['phone']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
        $full_phone   = $country_code . preg_replace('/[^0-9]/', '', $phone);

        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result      = $conn->query($check_email);

        if ($result->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, phone, password) VALUES ('$username', '$email', '$full_phone', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                $success = "Account created! Redirecting to login...";
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<style>
.register-wrapper {
    min-height: calc(100vh - 140px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
    background: #f0f2f5;
}
.register-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.10);
    width: 100%;
    max-width: 520px;
    padding: 40px 40px 32px;
}
.register-card .brand {
    text-align: center;
    margin-bottom: 28px;
}
.register-card .brand i {
    font-size: 2rem;
    color: var(--primary-color);
}
.register-card .brand h2 {
    margin: 10px 0 4px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #222;
}
.register-card .brand p {
    color: #888;
    font-size: 0.9rem;
    margin: 0;
}
.reg-alert {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.88rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.reg-alert.error   { background: #fff0f0; color: #c0392b; border: 1px solid #f5c6cb; }
.reg-alert.success { background: #f0fff4; color: #1e8449;  border: 1px solid #b2dfdb; }
.reg-field {
    margin-bottom: 18px;
}
.reg-field label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #444;
    margin-bottom: 6px;
}
.reg-input-wrap {
    position: relative;
}
.reg-input-wrap i.field-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: 14px;
    pointer-events: none;
}
.reg-input-wrap input {
    width: 100%;
    padding: 11px 12px 11px 38px;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    color: #333;
    box-sizing: border-box;
    transition: border 0.2s, box-shadow 0.2s;
    background: #fafafa;
}
.reg-input-wrap input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 41,128,185), 0.1);
    outline: none;
    background: #fff;
}
.reg-input-wrap .toggle-pw {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #aaa;
    font-size: 14px;
    padding: 2px;
}
.reg-input-wrap .toggle-pw:hover { color: #555; }
/* Phone row */
.phone-row {
    display: flex;
    gap: 8px;
}
.phone-row select {
    width: 148px;
    flex-shrink: 0;
    padding: 11px 10px;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-size: 13px;
    color: #444;
    background: #fafafa;
    cursor: pointer;
    transition: border 0.2s;
}
.phone-row select:focus {
    border-color: var(--primary-color);
    outline: none;
}
.phone-row .phone-input-wrap {
    flex: 1;
    position: relative;
}
.phone-row .phone-input-wrap i {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    font-size: 14px;
    pointer-events: none;
}
.phone-row .phone-input-wrap input {
    width: 100%;
    padding: 11px 12px 11px 38px;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    color: #333;
    box-sizing: border-box;
    background: #fafafa;
    transition: border 0.2s;
}
.phone-row .phone-input-wrap input:focus {
    border-color: var(--primary-color);
    outline: none;
    background: #fff;
}
.btn-register {
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark, #1a5276));
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 8px;
    transition: opacity 0.2s, transform 0.15s;
    letter-spacing: 0.3px;
}
.btn-register:hover { opacity: 0.92; transform: translateY(-1px); }
.register-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 13.5px;
    color: #777;
}
.register-footer a {
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
}
.register-footer a:hover { text-decoration: underline; }
</style>

<div class="register-wrapper">
    <div class="register-card">

        <div class="brand">
            <i class="fas fa-heartbeat"></i>
            <h2>Create Account</h2>
            <p>Join Ease Meds today — fast &amp; secure</p>
        </div>

        <?php if ($error): ?>
        <div class="reg-alert error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="reg-alert success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">

            <div class="reg-field">
                <label>Username</label>
                <div class="reg-input-wrap">
                    <i class="fas fa-user field-icon"></i>
                    <input type="text" name="username" required placeholder="Choose a username"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
            </div>

            <div class="reg-field">
                <label>Email Address</label>
                <div class="reg-input-wrap">
                    <i class="fas fa-envelope field-icon"></i>
                    <input type="email" name="email" required placeholder="your@email.com"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>

            <div class="reg-field">
                <label>Phone Number</label>
                <div class="phone-row">
                    <select name="country_code" required>
                        <option value="+977">🇳🇵 +977</option>
                        <option value="+91">🇮🇳 +91</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+44">🇬🇧 +44</option>
                        <option value="+61">🇦🇺 +61</option>
                        <option value="+86">🇨🇳 +86</option>
                        <option value="+81">🇯🇵 +81</option>
                        <option value="+49">🇩🇪 +49</option>
                        <option value="+33">🇫🇷 +33</option>
                        <option value="+7">🇷🇺 +7</option>
                        <option value="+971">🇦🇪 +971</option>
                        <option value="+92">🇵🇰 +92</option>
                        <option value="+880">🇧🇩 +880</option>
                        <option value="+94">🇱🇰 +94</option>
                        <option value="+65">🇸🇬 +65</option>
                        <option value="+60">🇲🇾 +60</option>
                        <option value="+66">🇹🇭 +66</option>
                        <option value="+82">🇰🇷 +82</option>
                        <option value="+39">🇮🇹 +39</option>
                        <option value="+34">🇪🇸 +34</option>
                        <option value="+55">🇧🇷 +55</option>
                        <option value="+27">🇿🇦 +27</option>
                        <option value="+20">🇪🇬 +20</option>
                        <option value="+966">🇸🇦 +966</option>
                    </select>
                    <div class="phone-input-wrap">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" required placeholder="Mobile number">
                    </div>
                </div>
            </div>

            <div class="reg-field">
                <label>Password</label>
                <div class="reg-input-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" name="password" id="pwInput" required placeholder="Create a password">
                    <button type="button" class="toggle-pw" onclick="togglePw('pwInput','pwEye')">
                        <i class="fas fa-eye" id="pwEye"></i>
                    </button>
                </div>
            </div>

            <div class="reg-field">
                <label>Confirm Password</label>
                <div class="reg-input-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input type="password" name="confirm_password" id="cpwInput" required placeholder="Repeat your password">
                    <button type="button" class="toggle-pw" onclick="togglePw('cpwInput','cpwEye')">
                        <i class="fas fa-eye" id="cpwEye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="register-footer">
            Already have an account? <a href="login.php">Login here</a>
        </div>

    </div>
</div>

<script>
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type    = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type    = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
