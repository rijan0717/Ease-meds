<?php
include 'header.php';
include 'config.php';
?>

<div class="container">
    <h1 class="page-title">Find your Doctor</h1>

    <div class="medicine-grid">
        <?php
        $sql = "SELECT * FROM doctors";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($doc = $result->fetch_assoc()):
                // Use placeholder if image is empty
                $img = !empty($doc['image']) ? $doc['image'] : 'https://via.placeholder.com/150?text=' . urlencode($doc['name']);
                ?>
                <div class="medicine-card" style="text-align: center;">
                    <div class="medicine-image-container" style="background: #eef2f3;">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($doc['name']); ?>"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="medicine-info">
                        <h3 class="medicine-title"><?php echo htmlspecialchars($doc['name']); ?></h3>
                        <span class="medicine-category"
                            style="font-size: 1rem; color: var(--secondary-color);"><?php echo htmlspecialchars($doc['specialty']); ?></span>
                        <p style="color: #666; margin-bottom: 15px;">Exp: <?php echo htmlspecialchars($doc['experience']); ?>
                        </p>

                        <div style="margin-bottom: 15px;">
                            <?php if (!empty($doc['bio_link'])): ?>
                                <a href="<?php echo htmlspecialchars($doc['bio_link']); ?>" target="_blank"
                                    style="color: var(--primary-color); font-size: 0.9rem;"><i class="fas fa-link"></i> View
                                    Profile</a>
                            <?php endif; ?>
                        </div>

                        <a href="book_appointment.php?doctor=<?php echo urlencode($doc['name']); ?>" class="btn-add-cart"
                            style="text-decoration: none;">
                            <i class="fas fa-calendar-check"></i> Book Appointment
                        </a>
                    </div>
                </div>
                <?php
            endwhile;
        else:
            echo "<p style='text-align:center; width:100%;'>No doctors available at the moment.</p>";
        endif;
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>