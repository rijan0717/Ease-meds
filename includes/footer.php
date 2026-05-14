</main>
<footer class="main-footer">
    <div class="container footer-container">
        <div class="footer-section">
            <h3>Ease Meds</h3>
            <p>Your trusted online pharmacy for quick and reliable medicine delivery.</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="/ease-meds/index.php">Home</a></li>
                <li><a href="/ease-meds/medicine.php">Medicines</a></li>
                <li><a href="/ease-meds/upload_prescription.php">Upload Prescription</a></li>
                <li><a href="/ease-meds/account.php">My Account</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p><i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal</p>
            <p><i class="fas fa-phone"></i> +977-9800000000</p>
            <p><i class="fas fa-envelope"></i> support@easemeds.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Ease Meds. Designed & Developed by <strong>Rijan & Shishir</strong>.</p>
    </div>
</footer>

<script>
    document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function () {
        document.querySelector('.main-nav').classList.toggle('active');
    });
</script>
</body>
</html>
