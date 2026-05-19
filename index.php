<?php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/config.php';

$contact_success = false;
$contact_error   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $c_name    = trim($_POST['contact_name']    ?? '');
    $c_email   = trim($_POST['contact_email']   ?? '');
    $c_subject = trim($_POST['contact_subject'] ?? '');
    $c_message = trim($_POST['contact_message'] ?? '');

    if (!$c_name || !$c_email || !$c_message) {
        $contact_error = 'Please fill in all required fields.';
    } elseif (!filter_var($c_email, FILTER_VALIDATE_EMAIL)) {
        $contact_error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)"
        );
        if ($stmt) {
            $stmt->bind_param('ssss', $c_name, $c_email, $c_subject, $c_message);
            $stmt->execute();
            $stmt->close();
        }
        $contact_success = true;
    }
}
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

    <!-- Contact Us Section -->
    <h2 class="page-title" id="contact">Contact Us</h2>
    <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 2.5rem; margin-bottom: 5rem; align-items: start;">

        <!-- Info Cards -->
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="background: #fff; border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-map-marker-alt" style="color: #fff; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h4 style="color: var(--text-dark); margin-bottom: 0.3rem; font-size: 1rem;">Our Address</h4>
                    <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5;">Baneshwor, Kathmandu<br>Bagmati Province, Nepal</p>
                </div>
            </div>

            <div style="background: #fff; border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-phone-alt" style="color: #fff; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h4 style="color: var(--text-dark); margin-bottom: 0.3rem; font-size: 1rem;">Phone</h4>
                    <p style="color: var(--text-light); font-size: 0.95rem;">+977-980-000-0001</p>
                    <p style="color: var(--text-light); font-size: 0.95rem;">+977-980-000-0002</p>
                </div>
            </div>

            <div style="background: #fff; border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-envelope" style="color: #fff; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h4 style="color: var(--text-dark); margin-bottom: 0.3rem; font-size: 1rem;">Email</h4>
                    <p style="color: var(--text-light); font-size: 0.95rem;">support@easemeds.com</p>
                    <p style="color: var(--text-light); font-size: 0.95rem;">info@easemeds.com</p>
                </div>
            </div>

            <div style="background: #fff; border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-clock" style="color: #fff; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h4 style="color: var(--text-dark); margin-bottom: 0.3rem; font-size: 1rem;">Working Hours</h4>
                    <p style="color: var(--text-light); font-size: 0.95rem;">Sun – Fri: 8:00 AM – 8:00 PM</p>
                    <p style="color: var(--text-light); font-size: 0.95rem;">Saturday: 9:00 AM – 5:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="background: #fff; border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 2.5rem;">
            <h3 style="color: var(--text-dark); margin-bottom: 0.5rem; font-size: 1.5rem;">Send Us a Message</h3>
            <p style="color: var(--text-light); margin-bottom: 2rem; font-size: 0.95rem;">Have a question or need help with your order? We&rsquo;re here for you.</p>

            <?php if ($contact_success): ?>
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-check-circle"></i>
                <span>Thank you! Your message has been received. We&rsquo;ll get back to you shortly.</span>
            </div>
            <?php elseif ($contact_error): ?>
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($contact_error); ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="#contact">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem; font-size: 0.9rem; color: var(--text-dark);">Full Name <span style="color:#e74c3c;">*</span></label>
                        <input type="text" name="contact_name" placeholder="Your name" required
                               value="<?php echo htmlspecialchars($_POST['contact_name'] ?? ''); ?>"
                               style="width:100%; padding: 11px 14px; border: 2px solid #eee; border-radius: 8px; font-family: inherit; font-size: 0.95rem; transition: border 0.3s; outline: none;"
                               onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='#eee'">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 500; margin-bottom: 0.4rem; font-size: 0.9rem; color: var(--text-dark);">Email Address <span style="color:#e74c3c;">*</span></label>
                        <input type="email" name="contact_email" placeholder="your@email.com" required
                               value="<?php echo htmlspecialchars($_POST['contact_email'] ?? ''); ?>"
                               style="width:100%; padding: 11px 14px; border: 2px solid #eee; border-radius: 8px; font-family: inherit; font-size: 0.95rem; transition: border 0.3s; outline: none;"
                               onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='#eee'">
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.4rem; font-size: 0.9rem; color: var(--text-dark);">Subject</label>
                    <input type="text" name="contact_subject" placeholder="How can we help?"
                           value="<?php echo htmlspecialchars($_POST['contact_subject'] ?? ''); ?>"
                           style="width:100%; padding: 11px 14px; border: 2px solid #eee; border-radius: 8px; font-family: inherit; font-size: 0.95rem; transition: border 0.3s; outline: none;"
                           onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='#eee'">
                </div>

                <div style="margin-bottom: 1.75rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.4rem; font-size: 0.9rem; color: var(--text-dark);">Message <span style="color:#e74c3c;">*</span></label>
                    <textarea name="contact_message" rows="5" placeholder="Write your message here..." required
                              style="width:100%; padding: 11px 14px; border: 2px solid #eee; border-radius: 8px; font-family: inherit; font-size: 0.95rem; resize: vertical; transition: border 0.3s; outline: none;"
                              onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='#eee'"><?php echo htmlspecialchars($_POST['contact_message'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="contact_submit" class="btn-place-order" style="width: auto; padding: 13px 40px; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>

    <style>
    @media (max-width: 768px) {
        #contact ~ div, #contact + div {
            grid-template-columns: 1fr !important;
        }
    }
    </style>

</div>
<?php include __DIR__ . '/includes/footer.php'; ?>