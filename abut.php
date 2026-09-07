<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/about.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="about-page">
        <section class="about-hero">
            <div class="about-hero-copy">
                <p class="about-eyebrow">About NSBM Marketplace</p>
                <h1>A better way to buy, sell, and reuse on campus.</h1>
                <p class="about-lead">NSBM Marketplace connects students with useful products, sustainable choices, and a community that keeps good things in circulation.</p>
                <a class="about-cta" href="shop.php">Explore the shop</a>
            </div>
            <div class="about-hero-art" aria-label="A collection of sustainable marketplace items" role="img">
                <span class="art-circle art-circle-one"></span>
                <span class="art-circle art-circle-two"></span>
                <span class="art-label">Campus<br>community</span>
            </div>
        </section>

        <section class="about-section">
            <div class="section-heading">
                <p class="about-eyebrow">What we believe</p>
                <h2>Small exchanges can make a lasting difference.</h2>
            </div>
            <div class="about-values">
                <article class="value-card">
                    <span class="value-number">01</span>
                    <h3>Useful by design</h3>
                    <p>Find practical items that make student life simpler, more affordable, and more connected.</p>
                </article>
                <article class="value-card">
                    <span class="value-number">02</span>
                    <h3>Community first</h3>
                    <p>Buy and sell with people in the NSBM community through a marketplace built for campus life.</p>
                </article>
                <article class="value-card">
                    <span class="value-number">03</span>
                    <h3>Better choices</h3>
                    <p>Give products a longer life and make room for more thoughtful, sustainable consumption.</p>
                </article>
            </div>
        </section>

        <section class="about-story">
            <div>
                <p class="about-eyebrow">Our purpose</p>
                <h2>Made for a smarter campus economy.</h2>
            </div>
            <p>NSBM Marketplace is a student-focused space for discovering, sharing, and exchanging products. From everyday essentials to study tools, every listing helps build a more resourceful campus community.</p>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
