<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/faq.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="faq-page">
        <section class="faq-intro">
            <div>
                <p class="faq-eyebrow">Need a hand?</p>
                <h1>Frequently asked questions.</h1>
            </div>
            <p class="faq-description">Answers to the questions we hear most from buyers and sellers on the NSBM Marketplace. Still stuck? <a href="contact.php">Contact us</a>.</p>
        </section>

        <section class="faq-accordion accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading1">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer1" aria-expanded="false" aria-controls="faqAnswer1">
                        How do I place an order?
                    </button>
                </h2>
                <div id="faqAnswer1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Browse the <a href="shop.php">shop</a>, add items to your cart, then go to checkout to enter your delivery details and choose a payment method. You'll get an order confirmation with an order number you can track from your profile.</div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer2" aria-expanded="false" aria-controls="faqAnswer2">
                        What payment methods are supported?
                    </button>
                </h2>
                <div id="faqAnswer2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">You can pay with Cash on Delivery, Card Payment, or Online Payment at checkout. Choose whichever works best for you before placing your order.</div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer3" aria-expanded="false" aria-controls="faqAnswer3">
                        Where can I see my order status?
                    </button>
                </h2>
                <div id="faqAnswer3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Go to your <a href="profile.php">profile page</a> and open "My Orders". Click any order to see its full status, items, and delivery details.</div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer4" aria-expanded="false" aria-controls="faqAnswer4">
                        How do returns work?
                    </button>
                </h2>
                <div id="faqAnswer4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">If something isn't right with your order, reach out through our <a href="contact.php">contact page</a> with your order number and we'll help sort out a return or replacement.</div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading5">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer5" aria-expanded="false" aria-controls="faqAnswer5">
                        Do I need an account to browse products?
                    </button>
                </h2>
                <div id="faqAnswer5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">No, you can browse and search the shop freely. You'll need to <a href="register.php">sign in or register</a> to add items to your wishlist, cart, or place an order.</div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading6">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqAnswer6" aria-expanded="false" aria-controls="faqAnswer6">
                        How do I update my account details?
                    </button>
                </h2>
                <div id="faqAnswer6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Open your <a href="profile.php">profile page</a> and select "Edit Profile" to update your name, phone number, or password.</div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
