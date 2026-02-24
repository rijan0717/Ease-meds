<?php
include 'header.php';
include 'config.php';
?>

<div class="container">
    <h1 class="page-title">Our Medicines</h1>

    <div class="medicine-grid">
        <?php
        // Fetch medicines from database
        $sql = "SELECT * FROM medicines";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Determine image URL
                // If database has full URL (like we inserted), use it. 
                // If just filename, valid logic would be needed. 
                // Our SQL insert uses full placeholder URLs, so we can use directly.
                $imgUrl = $row['image'];
                if (empty($imgUrl)) {
                    $imgUrl = "https://via.placeholder.com/300x200?text=" . urlencode($row['name']);
                }
                ?>
                <div class="medicine-card">
                    <div class="medicine-image-container">
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    </div>
                    <div class="medicine-info">
                        <?php if (isset($row['category'])): ?>
                            <span class="medicine-category"><?php echo htmlspecialchars($row['category']); ?></span>
                        <?php endif; ?>

                        <h3 class="medicine-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="medicine-price">Rs. <?php echo $row['price']; ?></div>

                        <form action="cart.php" method="POST">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                            <input type="hidden" name="medicine_id" value="<?php echo $row['id']; ?>">

                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 0.9rem;">Qty:
                                    <input type="number" name="quantity" value="1" min="1"
                                        max="<?php echo $row['quantity'] > 0 ? $row['quantity'] : 1; ?>"
                                        style="padding: 5px; width: 50px; border-radius: 4px; border: 1px solid #ddd;">
                                </label>
                            </div>
                            <?php if ($row['quantity'] > 0): ?>
                                <button type="submit" name="add_to_cart" class="btn-add-cart">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-add-cart" disabled
                                    style="background: #ccc; border-color: #ccc; cursor: not-allowed;">
                                    Out of Stock
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <h3>No medicines found in database.</h3>
                <p>Please import the <code>database.sql</code> file into your MySQL database.</p>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>