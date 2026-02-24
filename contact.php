<?php include 'header.php'; ?>

<div class="container">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 class="page-title">Contact Us</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px;">
            <div style="background: var(--white); padding: 30px; border-radius: 12px; box-shadow: var(--card-shadow);">
                <h3 style="margin-bottom: 20px;">Get in Touch</h3>
                <p style="color: var(--text-light); margin-bottom: 30px;">Have questions about your prescription or
                    order? Our team is here to help.</p>

                <div style="margin-bottom: 20px;">
                    <div style="font-weight: bold; margin-bottom: 5px; color: var(--primary-color);"><i
                            class="fas fa-map-marker-alt"></i> Address</div>
                    <p>Kathmandu, Nepal</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="font-weight: bold; margin-bottom: 5px; color: var(--primary-color);"><i
                            class="fas fa-phone"></i> Phone</div>
                    <p>+977-9800000000</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="font-weight: bold; margin-bottom: 5px; color: var(--primary-color);"><i
                            class="fas fa-envelope"></i> Email</div>
                    <p>support@easemeds.com</p>
                </div>
            </div>

            <div style="background: var(--white); padding: 30px; border-radius: 12px; box-shadow: var(--card-shadow);">
                <h3 style="margin-bottom: 20px;">Send Message</h3>
                <form>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" placeholder="Your Name"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="Your Email"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea rows="4" placeholder="How can we help?"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;"></textarea>
                    </div>
                    <button type="button" class="btn-place-order"
                        onclick="alert('Thank you for your message! We will get back to you soon.')">Send
                        Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>