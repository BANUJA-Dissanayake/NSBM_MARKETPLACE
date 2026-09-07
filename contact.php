<?php
session_start();
include "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="contact-page">
        <section class="contact-intro">
            <div>
                <p class="contact-eyebrow">Get in touch</p>
                <h1>Let’s talk about the marketplace.</h1>
            </div>
            <p>Have a question about a product, want to sell on campus, or need a hand with your order? Send us a message and our team will get back to you.</p>
        </section>

        <section class="contact-layout">
            <div class="contact-details">
                <div class="contact-detail-block">
                    <span class="contact-detail-label">Email support</span>
                    <a href="mailto:marketplace@nsbm.ac.lk">marketplace@nsbm.ac.lk</a>
                </div>
                <div class="contact-detail-block">
                    <span class="contact-detail-label">Phone</span>
                    <a href="tel:+94112345678">+94 11 234 5678</a>
                </div>
                <div class="contact-detail-block">
                    <span class="contact-detail-label">Community desk</span>
                    <p>NSBM Green University<br>Homagama, Sri Lanka</p>
                </div>
                <div class="contact-detail-block">
                    <span class="contact-detail-label">Response time</span>
                    <p>Monday - Friday<br>9:00 AM - 5:00 PM</p>
                </div>
                <div class="contact-note">
                    <span aria-hidden="true">↗</span>
                    <p>For product questions, include the product name so we can help you faster.</p>
                </div>
            </div>

            <form class="contact-form" onsubmit="submitContactMessage(event)">
                <div class="form-success" id="contactSuccess" role="status" hidden>Thanks, your message has been received.</div>
                <div class="form-error" id="contactError" role="alert" hidden></div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="name">Your name</label>
                        <input id="name" name="name" type="text" placeholder="Enter your name" required>
                    </div>
                    <div class="form-field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" placeholder="you@example.com" required>
                    </div>
                </div>
                <div class="form-field">
                    <label for="topic">What can we help with?</label>
                    <select id="topic" name="topic" required>
                        <option value="" disabled selected>Choose a topic</option>
                        <option value="product">Product question</option>
                        <option value="selling">Selling on campus</option>
                        <option value="order">Order support</option>
                        <option value="other">Something else</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" placeholder="Write your message here..." required></textarea>
                </div>
                <button class="contact-button" type="submit" id="contactSubmitBtn">Send message <span aria-hidden="true">→</span></button>
            </form>
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
