<?php
include 'header.php';
include 'config.php';
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
</div>

<?php include 'footer.php'; ?>