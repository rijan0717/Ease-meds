<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/config.php';
?>

<div class="container">
    <section class="hero-section" style="padding: 4rem 0; text-align: center;">
        <h1 style="font-size: 3rem; margin-bottom: 1rem;">Your Health, Delivered.</h1>
        <p style="font-size: 1.2rem; color: var(--text-light); margin-bottom: 2rem;">Order genuine medicines and
            healthcare products from the comfort of your home.</p>
        <a href="medicine.php" class="btn-place-order"
            style="display: inline-block; width: auto; padding: 15px 40px; text-decoration: none;">Order Medicine
            Now</a>
    </section>

    <h2 class="page-title">Featured Medicines</h2>
    <div class="medicine-grid">
        <!-- Displaying a few featured items (hardcoded for home page demo) -->
        <?php
        // Fetch 4 random medicines for display
        $sql = "SELECT * FROM medicines ORDER BY RAND() LIMIT 4";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($item = $result->fetch_assoc()):
                // Use stored image or placeholder
                $imgUrl = !empty($item['image']) ? $item['image'] : "https://via.placeholder.com/300x200?text=" . urlencode($item['name']);
                ?>
                <div class="medicine-card">
                    <div class="medicine-image-container">
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="medicine-info">
                        <span
                            class="medicine-category"><?php echo htmlspecialchars($item['category'] ?? 'Healthcare'); ?></span>
                        <h3 class="medicine-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="medicine-price">Rs. <?php echo $item['price']; ?></div>
                        <form action="cart.php" method="POST">
                            <input type="hidden" name="medicine_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn-add-cart">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
                <?php
            endwhile;
        else:
            echo "<p style='text-align:center; width:100%;'>No featured medicines available.</p>";
        endif;
        ?>
    </div>

    <!-- Doctors Section -->
    <h2 class="page-title">Meet Our Doctors</h2>
    <div class="medicine-grid" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); margin-bottom: 4rem;">
        <?php
        $dSql = "SELECT * FROM doctors ORDER BY RAND() LIMIT 4";
        $dResult = $conn->query($dSql);

        if ($dResult && $dResult->num_rows > 0):
            while ($doc = $dResult->fetch_assoc()):
                if (!empty($doc['image'])) {
                    $docImg = strpos($doc['image'], 'http') === 0
                        ? $doc['image']
                        : '/ease-meds/' . $doc['image'];
                } else {
                    $docImg = 'https://via.placeholder.com/200?text=Doctor';
                }
        ?>
        <div class="medicine-card" style="text-align: center;">
            <div class="medicine-image-container" style="height: 220px; background: #f1f2f6; display:flex; align-items:center; justify-content:center;">
                <img src="<?php echo htmlspecialchars($docImg); ?>"
                     alt="<?php echo htmlspecialchars($doc['name']); ?>"
                     style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 4px solid var(--primary-color);">
            </div>
            <div class="medicine-info">
                <span class="medicine-category"><?php echo htmlspecialchars($doc['specialty']); ?></span>
                <h3 class="medicine-title"><?php echo htmlspecialchars($doc['name']); ?></h3>
                <p style="font-size: 0.9rem; color: var(--text-light); margin-bottom: 1rem;"><?php echo htmlspecialchars($doc['experience']); ?></p>
                <a href="book_appointment.php?doctor_id=<?php echo (int)$doc['id']; ?>" class="btn-add-cart" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
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

    <!-- Prescription Section -->
    <div style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: var(--radius); padding: 3.5rem 2rem; margin-bottom: 4rem; text-align: center; color: #fff; box-shadow: 0 10px 30px rgba(0,184,148,0.3);">
        <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="fas fa-file-medical"></i></div>
        <h2 style="color: #fff; font-size: 2rem; margin-bottom: 0.75rem;">Upload Your Prescription</h2>
        <p style="font-size: 1.1rem; opacity: 0.9; max-width: 560px; margin: 0 auto 2rem;">Have a doctor&rsquo;s prescription? Upload it and we&rsquo;ll prepare your order with genuine medicines — delivered right to your door.</p>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
            <a href="upload_prescription.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 36px; background: #fff; color: var(--primary-color); font-weight: 700; border-radius: 8px; font-size: 1rem; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-upload"></i> Upload Prescription
            </a>
            <a href="doctors.php" style="display: inline-flex; align-items: center; gap: 8px; padding: 14px 36px; background: transparent; color: #fff; font-weight: 700; border-radius: 8px; font-size: 1rem; text-decoration: none; border: 2px solid rgba(255,255,255,0.7); transition: transform 0.2s;"
               onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-user-md"></i> Consult a Doctor
            </a>
        </div>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>