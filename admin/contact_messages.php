<?php
session_start();
include __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /ease-meds/login.php");
    exit();
}

// Mark as read
if (isset($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");
    header("Location: contact_messages.php");
    exit();
}

// Mark all as read
if (isset($_POST['mark_all_read'])) {
    $conn->query("UPDATE contact_messages SET is_read = 1");
    header("Location: contact_messages.php");
    exit();
}

// Delete message
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM contact_messages WHERE id = $id");
    header("Location: contact_messages.php");
    exit();
}

$unread_count = $conn->query("SELECT COUNT(*) AS c FROM contact_messages WHERE is_read = 0")->fetch_assoc()['c'];
$messages     = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Ease Meds Admin</title>
    <link rel="stylesheet" href="/ease-meds/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="admin-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 style="margin:0;">Contact Messages</h1>
                <?php if ($unread_count > 0): ?>
                <p style="color:var(--text-light); margin-top:4px; font-size:0.95rem;">
                    <span style="color:#e74c3c; font-weight:600;"><?php echo $unread_count; ?> unread</span> message<?php echo $unread_count > 1 ? 's' : ''; ?>
                </p>
                <?php endif; ?>
            </div>
            <?php if ($unread_count > 0): ?>
            <form method="POST">
                <button type="submit" name="mark_all_read"
                        style="padding:9px 20px; background:var(--primary-color); color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:0.9rem; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div style="background:#fff; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.05); overflow:hidden;">
            <?php if ($messages && $messages->num_rows > 0): ?>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:18%;">Name</th>
                            <th style="width:20%;">Email</th>
                            <th style="width:20%;">Subject</th>
                            <th style="width:25%;">Message</th>
                            <th style="width:12%;">Date</th>
                            <th style="width:10%; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $messages->fetch_assoc()):
                        $is_unread = !$row['is_read'];
                        $row_style = $is_unread ? 'background:#fffbf0;' : '';
                    ?>
                    <tr style="<?php echo $row_style; ?>">
                        <td style="color:var(--text-light);"><?php echo $row['id']; ?></td>
                        <td>
                            <div style="font-weight:<?php echo $is_unread ? '700' : '500'; ?>; display:flex; align-items:center; gap:6px;">
                                <?php if ($is_unread): ?>
                                <span style="width:8px; height:8px; background:#e74c3c; border-radius:50%; flex-shrink:0; display:inline-block;"></span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </div>
                        </td>
                        <td>
                            <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"
                               style="color:var(--secondary-color); font-size:0.9rem;">
                                <?php echo htmlspecialchars($row['email']); ?>
                            </a>
                        </td>
                        <td style="font-size:0.9rem; color:var(--text-dark);">
                            <?php echo $row['subject'] ? htmlspecialchars($row['subject']) : '<span style="color:#bbb;font-style:italic;">—</span>'; ?>
                        </td>
                        <td>
                            <div style="max-width:280px; font-size:0.88rem; color:var(--text-light); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                 title="<?php echo htmlspecialchars($row['message']); ?>">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </div>
                            <button onclick="toggleMessage(<?php echo $row['id']; ?>)"
                                    style="background:none; border:none; color:var(--secondary-color); font-size:0.8rem; cursor:pointer; padding:2px 0; margin-top:2px;">
                                <i class="fas fa-eye"></i> View full
                            </button>
                            <div id="msg-<?php echo $row['id']; ?>"
                                 style="display:none; margin-top:8px; padding:10px; background:#f9fbfd; border-radius:6px; font-size:0.88rem; color:var(--text-dark); white-space:pre-wrap; border-left:3px solid var(--primary-color);">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </div>
                        </td>
                        <td style="font-size:0.85rem; color:var(--text-light); white-space:nowrap;">
                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?><br>
                            <span style="font-size:0.8rem;"><?php echo date('h:i A', strtotime($row['created_at'])); ?></span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center; align-items:center;">
                                <?php if ($is_unread): ?>
                                <a href="?mark_read=<?php echo $row['id']; ?>"
                                   title="Mark as read"
                                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e8f5e9; color:#2e7d32; border-radius:6px; text-decoration:none;">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"
                                   title="Reply via email"
                                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#e3f2fd; color:#1565c0; border-radius:6px; text-decoration:none;">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>"
                                   title="Delete"
                                   onclick="return confirm('Delete this message?')"
                                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#fdecea; color:#c62828; border-radius:6px; text-decoration:none;">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div style="text-align:center; padding:60px 20px; color:#999;">
                <i class="fas fa-envelope-open-text" style="font-size:3rem; margin-bottom:15px; display:block; color:#ddd;"></i>
                <p style="font-size:1.1rem;">No contact messages yet.</p>
                <p style="font-size:0.9rem; margin-top:6px;">Messages submitted via the Contact Us form will appear here.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleMessage(id) {
    const el = document.getElementById('msg-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
