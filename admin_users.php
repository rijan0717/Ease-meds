<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <h1>Registered Users</h1>

            <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Handle Role Update
                            if (isset($_POST['toggle_role'])) {
                                $uid = $_POST['user_id'];
                                $action = $_POST['action'];
                                $new_role = ($action == 'make_admin') ? 'admin' : 'user';

                                $conn->query("UPDATE users SET role='$new_role' WHERE id='$uid'");
                                echo "<script>window.location.href='admin_users.php';</script>";
                            }

                            $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                            while ($row = $result->fetch_assoc()) {
                                $is_admin = $row['role'] === 'admin';
                                echo "<tr>
                                <td>{$row['id']}</td>
                                <td style='font-weight: 500; color: #2d3436;'>" . htmlspecialchars($row['username']) . "</td>
                                <td style='color: #636e72;'>" . htmlspecialchars($row['email']) . "</td>
                                <td>
                                    <span style='padding: 4px 10px; background: " . ($is_admin ? '#ffeaa7' : '#e3f2fd') . "; color: " . ($is_admin ? '#d35400' : '#0984e3') . "; border-radius: 20px; font-size: 0.8rem; font-weight: 600;'>
                                        " . ucfirst($row['role']) . "
                                    </span>
                                </td>
                                <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='user_id' value='{$row['id']}'>
                                        " . ($is_admin ?
                                    "<input type='hidden' name='action' value='revoke'>
                                         <button type='submit' name='toggle_role' style='color: #d35400; background: none; border: 1px solid #d35400; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; transition:0.3s;'>Revoke Admin</button>" :
                                    "<input type='hidden' name='action' value='make_admin'>
                                         <button type='submit' name='toggle_role' style='color: var(--primary-color); background: none; border: 1px solid var(--primary-color); padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; transition:0.3s;'>Make Admin</button>"
                                ) . "
                                    </form>
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