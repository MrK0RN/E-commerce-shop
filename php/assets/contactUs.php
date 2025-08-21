<section class="contact" id="contact">
    <div class="container">
        <div class="section-title">
            <h2>Have Questions? Contact Us!</h2>
            <p>Get in touch for a free consultation and quote</p>
        </div>
        <div class="contact-container">
            <div class="contact-info">
                <h3>Our Contact Information</h3>
                <p>Feel free to reach out to us with any questions about our roller shutters or to schedule a consultation.</p>
                <div class="contact-details">
                    <?php
                        include "modules/ContactUs.php";
                    ?>
                </div>
            </div>
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form id="form">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Request</button>
                </form>
            </div>
        </div>
    </div>
</section>
    