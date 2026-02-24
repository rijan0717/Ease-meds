<?php
include 'config.php';

$username = "Rijan Admin";
$email = "rijan@gmail.com";
$password = "Password@123";
$role = "admin";

// check if exists
$check = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($check->num_rows > 0) {
    // Update password just in case
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password='$hashed_password', role='$role' WHERE email='$email'";
    if ($conn->query($sql)) {
        echo "Admin user '$email' updated successfully. Password reset.";
    } else {
        echo "Error updating admin: " . $conn->error;
    }
} else {
    // Insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
    if ($conn->query($sql)) {
        echo "Admin user '$email' created successfully.";
    } else {
        echo "Error creating admin: " . $conn->error;
    }
}
?>
<br>
<a href="easemeds-admin/">Go to Admin Login</a>